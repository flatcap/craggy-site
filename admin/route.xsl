<?xml version="1.0" encoding="ISO-8859-1"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

<xsl:template match="/">
	<xsl:variable name="route_exists" select="//route[1]" />
	<xsl:variable name="route_id_exists" select="//route_id[1]" />
	<xsl:variable name="id_exists" select="//id[1]" />
	<xsl:variable name="error_exists" select="//error[1]" />
	<xsl:variable name="climb_type_exists" select="//climb_type[1]" />
	<xsl:variable name="success_exists" select="//success[1]" />
	<xsl:variable name="difficulty_exists" select="//difficulty[1]" />
	<xsl:variable name="nice_exists" select="//nice[1]" />
	<xsl:variable name="onsight_exists" select="//onsight[1]" />
	<xsl:variable name="setter_exists" select="//setter[1]" />
	<xsl:variable name="date_exists" select="//date[1]" />
	<xsl:variable name="note_exists" select="//notes[1]" />
	<xsl:variable name="global_error_exists" select="/list/error[1]" />
	<html>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		<title>List of <xsl:value-of select="/list/@type"/>s</title>
		<style type="text/css">
			th { background: #ddd; }
			tr.valid { background: #cfc; }
			tr.invalid { background: #fcc; }
			h1 { margin: 0; font-size: 120%; margin: 0.5em 0 0.5 0; }
			div.error { background: orange; padding: 0.5em; }
		</style>
	<head>
	</head>
	<body>
	<xsl:if test="$route_exists">
	<h1>List of <xsl:value-of select="/list/@type"/>s:</h1>
	<table border="1" cellspacing="0" cellpadding="3">
		<tr>
			<xsl:if test="$route_id_exists">
			<th>Route ID</th>
			</xsl:if>
			<xsl:if test="$id_exists">
			<th>ID</th>
			</xsl:if>
			<th>Panel</th>
			<th>Colour</th>
			<th>Grade</th>
			<xsl:if test="$climb_type_exists">
			<th>Type</th>
			</xsl:if>
			<xsl:if test="$setter_exists">
			<th>Setter</th>
			</xsl:if>
			<xsl:if test="$success_exists">
			<th>Success</th>
			</xsl:if>
			<xsl:if test="$difficulty_exists">
			<th>Difficulty</th>
			</xsl:if>
			<xsl:if test="$nice_exists">
			<th>Nice</th>
			</xsl:if>
			<xsl:if test="$onsight_exists">
			<th>Onsight</th>
			</xsl:if>
			<xsl:if test="$date_exists">
			<th>Date</th>
			</xsl:if>
			<xsl:if test="$note_exists">
			<th>Notes</th>
			</xsl:if>
			<xsl:if test="$error_exists">
			<th>Errors</th>
			</xsl:if>
		</tr>
		<xsl:for-each select="//route">
			<xsl:element name='tr'>
			<xsl:attribute name='class'>
			<xsl:value-of select="@result"/>
			</xsl:attribute>
				<xsl:if test="$route_id_exists">
				<td><xsl:value-of select="route_id"/></td>
				</xsl:if>
				<xsl:if test="$id_exists">
				<td><xsl:value-of select="id"/></td>
				</xsl:if>
				<td><xsl:value-of select="panel"/></td>
				<td><xsl:value-of select="colour"/></td>
				<td><xsl:value-of select="grade"/></td>
				<xsl:if test="$climb_type_exists">
				<td><xsl:value-of select="climb_type"/></td>
				</xsl:if>
				<xsl:if test="$setter_exists">
				<td><xsl:value-of select="setter"/></td>
				</xsl:if>
				<xsl:if test="$success_exists">
				<td><xsl:value-of select="success"/></td>
				</xsl:if>
				<xsl:if test="$difficulty_exists">
				<td><xsl:value-of select="difficulty"/></td>
				</xsl:if>
				<xsl:if test="$nice_exists">
				<td><xsl:value-of select="nice"/></td>
				</xsl:if>
				<xsl:if test="$onsight_exists">
				<td><xsl:value-of select="onsight"/></td>
				</xsl:if>
				<xsl:if test="$date_exists">
				<td><xsl:value-of select="date"/></td>
				</xsl:if>
				<xsl:if test="$note_exists">
				<td><xsl:value-of select="notes"/></td>
				</xsl:if>
				<xsl:if test="$error_exists">
				<td>
				<xsl:for-each select="error">
				<xsl:value-of select="."/><xsl:element name="br"/>
				</xsl:for-each>
				</td>
				</xsl:if>
			</xsl:element>
		</xsl:for-each>
	</table>
	</xsl:if>
	<xsl:if test="$global_error_exists">
	<xsl:element name="br" />
	<div class="error">
	<h1>Errors:</h1>
	<xsl:for-each select="/list/error">
	<xsl:value-of select="."/><xsl:element name="br" />
	</xsl:for-each>
	</div>
	</xsl:if>
	</body>
	</html>
</xsl:template>
</xsl:stylesheet>

