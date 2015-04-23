<?php
define('PATTERN_NUMEROS', '[0-9&#46;&#44;]+');
define('PATTERN_DATALIST_ESTADOS', '[1-3]{1}');
define('PATTERN_DATALIST_BASE', '[1-9]');
define('PATTERN_AÑO', '\d{4}');
define('PATTERN_FECHA', '\d{4}-\d{1,2}-\d{1,2}');
define('PATTERN_PALABRA', '[a-zA-Z&Aacute;&Eacute;&Iacute;&Oacute;&Uacute;&aacute;&eacute;&iacute;&oacute;&uacute;]');
define('PATTERN_PALABRAS_ESPACIOS', '[a-zA-Z&nbsp;&Aacute;&Eacute;&Iacute;&Oacute;&Uacute;&aacute;&eacute;&iacute;&oacute;&uacute; ]{2,500}');
define('PATTERN_NUMEROS_FINANCIEROS', '[0-9&#46;]{1,19}');
define('PATTERN_ALFANUMERICO', '[a-zA-Z0-9&nbsp;&Aacute;&Eacute;&Iacute;&Oacute;&Uacute;&aacute;&eacute;&iacute;&oacute;&uacute;&#45;&#46;&#44;]');
define('TITLE_NUMEROS', 'Ingrese valores numéricos');
define('TITLE_AÑO', 'Ingrese el valor de un año');
define('TITLE_FECHA', 'Ingrese una fecha en el formato AAAA-MM-DD');
define('TITLE_DOCUMENTO', 'Seleccione un documento');
define('TITLE_ALFANUMERICO', 'Digite letras y números');
define('TITLE_NUMEROS_FINANCIEROS', 'Ingrese un valor numérico, máximo 15 números.');
define('TITLE_PALABRAS_ESPACIOS', 'Digite letras y espacios');
define('PATTERN_SELECT', '[1-9]{1}');
define('PATTERN_EMAIL', '^[-\w.]+@{1}[-a-z0-9]+[.]{1}[a-z]{2,10}+[.]{0,1}[a-z]{2,10}$');
define('TITLE_EMAIL', 'Digite en el formato : aa@aaa.com')
?>