import pysolr
from pymarc import MARCReader, MARCWriter
from io import BytesIO
import argparse
import sys

class NOTFoundError(Exception):
    pass

class Shelf:
    solr = pysolr.Solr('http://localhost:8080/solr/biblio', always_commit=True)
    keys_array = ['spellingShingle', 'title_fullStr', 'title_full_unstemmed', 'author_facet', 'publisherStr', 'topic_unstemmed', 'allfields_unstemmed', 'fulltext_unstemmed', 'topic_browse', 'author_browse', 'dewey-search', 'callnumber-search']
    shelf = ''

    def __init__(self, shelf):
        self.shelf = shelf

    def update(self, isbn):
        results = self.solr.search('isbn:%s' % isbn)
        if (len(results) != 1):
            raise NOTFoundError

        for result in results:
            solr_rec = result
            # marc取り出し
            reader = MARCReader(solr_rec['fullrecord'].encode())
            record = next(reader)
            # shelf付け替え
            record['852']['a'] = self.shelf
            # 変更後のmarc書き出し
            memory = BytesIO()
            writer = MARCWriter(memory)
            writer.write(record)
            new_marc = memory.getvalue().decode()
            memory.close()
            # Solr更新データの作成
            solr_rec['shelf'] = self.shelf
            solr_rec['fullrecord'] = new_marc
            # コピーフィールドの削除
            for key in self.keys_array:
                try:
                    del solr_rec[key]
                except KeyError:
                    pass
            # solrデータ更新
            self.solr.add([solr_rec])

def main():
    parser = argparse.ArgumentParser()
    parser.add_argument('shelf', help='shelf code for these isbns')
    parser.add_argument('infile', nargs='?', type=argparse.FileType(),
                        default=sys.stdin, help='isbn file (default: stdin)')
    args = parser.parse_args()
    total, ok, ng = 0, 0, 0
    with args.infile as f:
        shelf = Shelf(args.shelf)
        for isbn in f:
            total += 1
            try:
                shelf.update(isbn)
                ok += 1
            except NOTFoundError:
                print("Not 1 copy: %s" % (isbn), sys.stderr)
                ng += 1
            except:
                print("Error: %s" % (isbn), sys.stderr)
                ng += 1

    print("total: % 5d, ok: % 5d, ng: % 5d" % (total, ok, ng))

if __name__ == "__main__":
    main()
