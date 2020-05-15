package jp.zuki_ebetsu.index;
/**
 * Routines for JPMarc specific items.
 *
 * Copyright (C) Villanova University 2017.
 */

import java.util.ArrayList;
import java.util.Collection;
import java.util.Iterator;
import java.util.LinkedHashSet;
import java.util.LinkedList;
import java.util.List;
import java.util.Set;
import org.marc4j.marc.Record;
import org.marc4j.marc.VariableField;
import org.marc4j.marc.ControlField;
import org.marc4j.marc.DataField;
import org.marc4j.marc.Subfield;
import org.solrmarc.callnum.CallNumUtils;
import org.solrmarc.callnum.DeweyCallNumber;
import org.solrmarc.callnum.LCCallNumber;
import org.solrmarc.index.SolrIndexer;

public class JPMarcTools
{
    /**
     * Make id from 001 and 003
     * @param Reocrd record
     * @return ID
     */
    public String getID(Record record) {
        ControlField cnField = (ControlField) record.getVariableField("001");
        ControlField cniField = (ControlField) record.getVariableField("003");
        String cni = "LC";
        if (cniField != null) {
            String data = cniField.getData().toUpperCase();
            if ("JTNDL".equals(data)) {
                cni = "JP";
            } else if ("ORIGINAL".equals(data)) {
                cni = "OR";
            }
        }

        return cni + cnField.getData().toUpperCase();
    }

    /**
     * Make normalized isbn (13 and 10)
     * @param Reocrd record
     * @param String field spec
     * @return Set<String> set of isbns
     */
    public Set<String> getNormalizedISBNs(Record record, String field) {
        Set<String> result = new LinkedHashSet<String>();

        Set<String> isbns = SolrIndexer.instance().getFieldList(record, field);
        Iterator<String> iter = isbns.iterator();
        String current;
        while(iter.hasNext()) {
            current = iter.next().toUpperCase().replaceAll("[^0-9X]", "");
            if (current.length() == 10)
                result.add(getISBN13(current));
            else if (current.length() == 13)
                result.add(getISBN10(current));
            else
                continue;
            result.add(current);
        }
        return result;
    }

    /**
     * Extract ISSN from a record and return them in a normalized format
     * @param Rocord record
     * @param String fieldSpec
     * @return Set<String>  noormalized ISSNs
     */
    public Set<String> getNormalizedISSNs(Record record, String fieldSpec) {
        Set<String> result = new LinkedHashSet<String>();

        Set<String> issns = SolrIndexer.instance().getFieldList(record, fieldSpec);
        Iterator<String> iter = issns.iterator();
        String current;
        while(iter.hasNext()) {
            current = iter.next().toUpperCase().replaceAll("[^0-9X]", "");
            if (current != null && current.length() > 0) {
                result.add(current);
            }
        }

        return result;
    }

    /**
     * Extract the subject component of the call number
     *
     * Can return null
     *
     * @param record
     * @return Call number label
     */
    public String getNDLSubject(Record record, String fieldSpec) {
        return getNDLNumber(record, fieldSpec);
    }

    public String getNDLFirstNumber(Record record, String fieldSpec) {
        String val = getNDLNumber(record, fieldSpec);
        if (val != null && !"".equals(val)) {
            val = val.substring(0, 1);
        }
        return val;
    }

    /**
     * Extract NDL code.
     *   Return the code not to start without 'V-Z' if possible.
     *
     * @param record
     * @return NDL code
     */
    public String getNDLNumber(Record record, String fieldSpec) {
        String first = null;
        Set vals = SolrIndexer.instance().getFieldList(record, fieldSpec);
        Iterator<String> iter = vals.iterator();
        while(iter.hasNext()) {
            String val = iter.next();
            if (CallNumUtils.isValidDewey(val)) {
                continue;
            }
            // For electronized materials: its callnumber starts with number or small char
            char firstChar = val.charAt(0);
            if ((firstChar >= '0' && firstChar <= '9') || (firstChar >= 'a' && firstChar <= 'z')) {
                val = "YY-" + val;
            }
            String [] ndlValues = val.toUpperCase().split("[^A-Z]+");
            if (ndlValues.length > 0)
            {
                if (ndlValues[0] == null || "".equals(ndlValues[0].trim())) {
                    first = "YY";
                } else {
                    firstChar = ndlValues[0].charAt(0);
                    if (firstChar < 'V') {
                        return ndlValues[0];
                    } else if (first == null) {
                        first = ndlValues[0];
                    }
                }
            }
        }
        return (first == null ? "YY" : first);
    }

    /**
     * Extract all searchable fields, ISBNs and ISSNs
     * @param record
     * @param lowerBoundStr
     * @param upperboundStr
     * @return String of the all fields
     */
    public Set<String> getAllSearchableFieldsAsSet(Record record, String lowerBoundStr, String upperBoundStr) {
        Set<String> fields = SolrIndexer.instance().getAllSearchableFieldsAsSet(record, lowerBoundStr, upperBoundStr);
        Set<String> isbns = getNormalizedISBNs(record, "020a");
        Set<String> issns = getNormalizedISSNs(record, "022a:440x:490x:730x:776x:780x:785x");

        fields.addAll(isbns);
        fields.addAll(issns);

        return fields;
    }

    /**
     * Make collection from 852 field
     * @param Reocrd record
     * @return String collection
     */
    public String getCollection(Record record) {
        String col = "zuki";

        DataField field = (DataField) record.getVariableField("852");
        if (field != null &&
            field.getSubfield('a') != null &&
            field.getSubfield('a').getData().charAt(0) == 'H')
            col = "jako";

        return col;
    }

    private String getISBN13(String id) {
        id = "978" + id.substring(0, 9);
        return (id + checkDigit13(id));
    }

    private String getISBN10(String id) {
        id = id.substring(3, 12);
        return (id + checkDigit10(id));
    }

    private String checkDigit13(String isbn) {
        String NUMVALUES = "0123456789";
        char[] cary = isbn.toCharArray();
        int sum = 0;
        int val;
        for (int i=0; i<12; i++) {
            val = NUMVALUES.indexOf(cary[i]);
            sum += val * (i % 2 == 0 ? 1 : 3);
        }
        return (sum % 10 == 0) ? "0" : String.valueOf(10 - (sum % 10));
    }

    private String checkDigit10(String isbn) {
        String NUMVALUES = "0123456789";
        int[] weight = { 10, 9, 8, 7, 6, 5, 4, 3, 2 };

        char[] cary = isbn.toCharArray();
        int sum = 0;
        int val;
        for (int i=0; i<9; i++) {
            val = NUMVALUES.indexOf(cary[i]);
            sum += val * weight[i];
        }
        int rem = sum % 11;
        return (rem == 0) ? "0" :
               (rem == 1) ? "X" :
               String.valueOf(11 - rem);
    }

}
