<?xml version="1.0" encoding="ISO-8859-1"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

<xsl:template match="/">
	<xsl:variable name="id_exists" select="//id[1]" />
	<xsl:variable name="message_exists" select="//message[1]" />
	<xsl:variable name="setter_exists" select="//setter[1]" />
	<xsl:variable name="date_exists" select="//date[1]" />
	<xsl:variable name="notes_exists" select="//notes[1]" />
	<html>
	<body>
	<h2>List of <xsl:value-of select="/list/@type"/>s:</h2>
	<table border="1" cellspacing="0" cellpadding="3">
		<tr>
			<xsl:if test="string($id_exists)">
			<th>ID</th>
			</xsl:if>
			<th>Panel</th>
			<th>Colour</th>
			<th>Grade</th>
			<xsl:if test="string($setter_exists)">
			<th>Setter</th>
			</xsl:if>
			<xsl:if test="string($date_exists)">
			<th>Date</th>
			</xsl:if>
			<xsl:if test="string($notes_exists)">
			<th>Notes</th>
			</xsl:if>
			<xsl:if test="string($message_exists)">
			<th>Message</th>
			</xsl:if>
		</tr>
		<xsl:for-each select="//route">
			<tr>
				<xsl:if test="string($id_exists)">
				<td><xsl:value-of select="id"/></td>
				</xsl:if>
				<td><xsl:value-of select="panel"/></td>
				<td><xsl:value-of select="colour"/></td>
				<td><xsl:value-of select="grade"/></td>
				<xsl:if test="string($setter_exists)">
				<td><xsl:value-of select="setter"/></td>
				</xsl:if>
				<xsl:if test="string($date_exists)">
				<td><xsl:value-of select="date"/></td>
				</xsl:if>
				<xsl:if test="string($notes_exists)">
				<td><xsl:value-of select="notes"/></td>
				</xsl:if>
				<xsl:if test="string($message_exists)">
				<td><xsl:value-of select="message"/></td>
				</xsl:if>
			</tr>
		</xsl:for-each>
	</table>
	</body>
	</html>
</xsl:template>
</xsl:stylesheet>

