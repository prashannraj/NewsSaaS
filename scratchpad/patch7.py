p='database/migrations/2026_10_25_074100_create_invoices_table.php'
s=open(p).read()
s=s.replace('Schema::dropIfExists(\'invoices\');', 'Schema::dropIfExists(\'billing_invoices\');')
open(p,'w').write(s)
print('done')
