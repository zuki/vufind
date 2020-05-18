<?xml version="1.0" encoding="utf-8"?>

<xsl:stylesheet version="1.0"
                xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
                xmlns:zs="http://www.loc.gov/zing/srw/"
                xmlns:marc="http://www.loc.gov/MARC21/slim">
  <xsl:output method="xml" indent="yes"/>
  <xsl:template match="/">
    <ResultSet>
      <RecordCount><xsl:value-of select="//zs:numberOfRecords"/></RecordCount>
      <xsl:call-template name="doc"/>
    </ResultSet>
  </xsl:template> 
    
  <xsl:template name="doc">
    <xsl:for-each select="//zs:records/zs:record/zs:recordData/marc:record">
      <xsl:element name="record">
        <xsl:apply-templates/>
      </xsl:element>
    </xsl:for-each>
  </xsl:template>

  <!-- borrowed from http://wiki.tei-c.org/index.php/Remove-Namespaces.xsl: -->
  <xsl:template match="*">
    <xsl:element name="{local-name()}">
      <xsl:apply-templates select="@*|node()"/>
    </xsl:element>
  </xsl:template>

  <xsl:template match="@*">
    <xsl:attribute name="{local-name()}">
      <xsl:value-of select="."/>
    </xsl:attribute>
  </xsl:template>

</xsl:stylesheet>
