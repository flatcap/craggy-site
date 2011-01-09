<?xml version="1.0" encoding="ISO-8859-1"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

<xsl:template match="/">
  <html>
  <body>
  <h2>Routes:</h2>
    <table border="1">
      <tr bgcolor="#9acd32">
        <th>ID</th>
        <th>Panel</th>
        <th>Colour</th>
        <th>Grade</th>
      </tr>
      <xsl:for-each select="route_list/route">
        <tr>
          <td><xsl:value-of select="id"/></td>
          <td><xsl:value-of select="panel"/></td>
          <td><xsl:value-of select="colour"/></td>
          <td><xsl:value-of select="grade"/></td>
        </tr>
      </xsl:for-each>
    </table>
  </body>
  </html>
</xsl:template>
</xsl:stylesheet>

