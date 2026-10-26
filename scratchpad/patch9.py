p='database/migrations/2026_10_26_000010_upgrade_cms_tables.php'
s=open(p).read()
s=s.replace("            $table->dropUnique(['organization_id', 'slug']);\n", "")
open(p,'w').write(s)
print('done')
