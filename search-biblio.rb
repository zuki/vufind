# -*- coding: utf-8 -*-
require 'rsolr'

def getISBN(isbn10, isbn13)
  return isbn13.delete("-") if isbn13 != ""
  return "" if isbn10 == ""
  src = "978#{isbn10.delete("-")[0..8]}"
  sum = src.split(//).map{|n| n.to_i}
          .reduce(0){|s, n| s + (n % 2 == 0) ? n : 3 * n}
  rem = 10 - sum % 10
  return src + (rem == 10 ? "0" : rem.to_s)
end
  
id = 0
#puts ['"id"', '"marcno"', '"shelf"', '"title"', '"pub"', '"isbn"'].join(",")
solr = RSolr.connect :url => 'http://localhost:8080/solr/biblio'
for i in 0..11 do
  res = solr.get 'select', :params => {
    :q => "*:*",
    :start => (i * 1000 + 1),
    :rows => 1000
  }
  #break if (res['response']['numFound'] == 0)
  #STDERR.puts "i: #{i}, count: #{res['response']['numFound']}"  
  res['response']['docs'].each do |doc|
    id = id + 1
    marcno = doc['id']
    shelf = doc['shelf']
    title = doc['title_full']
    title = title[0..-3] if title.end_with?(" /")
    title = title[0..-2] if title.end_with?(".")
    pub   = doc['publisher'][0] if doc['publisher']
    if (doc['publishDate'])
      pub   += (pub != "" ? " " : "") + doc['publishDate'][0]
    end
    isbns = doc['isbn'] ? doc['isbn'] : []
    isbn10 = isbn13 = ""
    isbns.each do |isbn|
      isbn10 = isbn if isbn.length == 10
      isbn13 = isbn if isbn.length == 13
    end
    isbn = getISBN(isbn10, isbn13)
    puts [id, marcno, shelf, title, pub, isbn].join("\t")
  end
end
