# -*- coding: utf-8 -*-
require 'rsolr'
require 'json'

solr = RSolr.connect :url => 'http://localhost:8080/solr/biblio'
res = solr.get 'select', :params => {
  :q => "*:*",
  :rows => 50
}

res['response']['docs'].each do |doc|
  puts doc.to_json
end
