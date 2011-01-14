<?xml version="1.0" encoding="ISO-8859-1"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

<xsl:template match="/">
	<xsl:variable name="id_exists" select="//id[1]" />
	<xsl:variable name="message_exists" select="//message[1]" />
	<xsl:variable name="setter_exists" select="//setter[1]" />
	<xsl:variable name="date_exists" select="//date[1]" />
	<xsl:variable name="notes_exists" select="//notes[1]" />
	<html>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		<title>List of <xsl:value-of select="/list/@type"/>s</title>
		<style type="text/css">
			th { background: #ddd; }
			tr.success { background: #cfc; }
			tr.failure { background: #fcc; }
		</style>
	<head>
	</head>
	<body>
	<h2>List of <xsl:value-of select="/list/@type"/>s:</h2>
	<table border="1" cellspacing="0" cellpadding="3">
		<tr>
			<xsl:if test="$id_exists">
			<th>ID</th>
			</xsl:if>
			<th>Panel</th>
			<th>Colour</th>
			<th>Grade</th>
			<xsl:if test="$setter_exists">
			<th>Setter</th>
			</xsl:if>
			<xsl:if test="$date_exists">
			<th>Date</th>
			</xsl:if>
			<xsl:if test="$notes_exists">
			<th>Notes</th>
			</xsl:if>
			<xsl:if test="$message_exists">
			<th>Message</th>
			</xsl:if>
		</tr>
		<xsl:for-each select="//route">
			<xsl:element name='tr'>
			<xsl:attribute name='class'>
			<xsl:value-of select="@result"/>
			</xsl:attribute>
				<xsl:if test="$id_exists">
				<td><xsl:value-of select="id"/></td>
				</xsl:if>
				<td><xsl:value-of select="panel"/></td>
				<td><xsl:value-of select="colour"/></td>
				<td><xsl:value-of select="grade"/></td>
				<xsl:if test="$setter_exists">
				<td><xsl:value-of select="setter"/></td>
				</xsl:if>
				<xsl:if test="$date_exists">
				<td><xsl:value-of select="date"/></td>
				</xsl:if>
				<xsl:if test="$notes_exists">
				<td><xsl:value-of select="notes"/></td>
				</xsl:if>
				<xsl:if test="$message_exists">
				<td>
				<xsl:for-each select="message">
				<xsl:value-of select="."/><xsl:element name="br"/>
				</xsl:for-each>
				</td>
				</xsl:if>
			</xsl:element>
		</xsl:for-each>
	</table>
	</body>
	</html>
</xsl:template>
</xsl:stylesheet>

