Unealta pentru traducerile in Romana din Prestashop
===================================================

Script PHP pentru procesarea in vrac a traducerilor in romana:

1. conversia diacriticelor ăâîșțĂÂÎȘȚ in caractere simple aaistAAIST

2. dezactivarea (prin comentare) traducerilor goale pentru a remedia lipsa verificarii de sir gol in codul de traducere din PrestaShop

MOD DE UTILIZARE

Copiati prestashop_i18n_ro_tool.php in directorul radacina PrestaShop, apoi rulati-l.

Momentan salveaza vechile traduceri in /traduceri/export/ro_yymmdd-hhmmss/. Vechile traducerile pot fi apoi restaurate prin copierea lor peste directorul radacina PrestaShop.


AVERTIZARE

A FOST TESTAT DOAR PE MASINA MEA LOCALA. ASIGURA-TE CA AI BACKUP-URI SI CA TI-AI LUAT TOATE MASURILE DE SIGURANTA. IN CELE DIN URMA, DACA CEVA NU FUNCTIONEAZA, ESTE SITE-UL SI RESPONSABILITATEA TA - VOI INCERCA SA AJUT, DAR NU VOI ACCEPTA NICI O RESPONSABILITATE PENTRU EVENTUALELE PROBLEME.

ISTORIE

2014-06-23 Prima versiune testata pe o instalare curata de 1.6.0.6

2016-11-11 Am adaugat dezactivarea prin comentare a traducerilor goale; testata pe 1.6.1.8 si 1.6.1.9
