<xsl:stylesheet version="1.0" 
                xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
                xmlns:zs="http://www.loc.gov/zing/srw/">
  <xsl:output method="xml"/>
  <xsl:template match="/">
    <ResultSet>
      <RecordCount><xsl:value-of select="//zs:numberOfRecords"/></RecordCount>
      <xsl:call-template name="facet"/>
      <xsl:call-template name="doc"/>
    </ResultSet>
  </xsl:template>

  <xsl:template name="facet">
    <Facets>
      <xsl:for-each select="//zs:facets/zs:lst">
        <Cluster>
          <xsl:attribute name="name"><xsl:value-of select="@name"/></xsl:attribute>
          <xsl:for-each select="./zs:int">
            <item>
              <xsl:attribute name="count"><xsl:value-of select="."/></xsl:attribute>
              <xsl:value-of select="@name"/>
            </item>
          </xsl:for-each>
        </Cluster>
      </xsl:for-each>
    </Facets>
  </xsl:template>

  <xsl:template name="doc">
    <xsl:for-each select="//dcndl_simple:dc" xmlns:dcndl_simple="http://ndl.go.jp/dcndl/dcndl_simple/">
      <record>
        <xsl:apply-templates/>
      </record>
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

