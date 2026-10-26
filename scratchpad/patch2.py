p='database/migrations/2026_10_26_000010_upgrade_cms_tables.php'
s=open(p).read()
old="            $table->string('seo_title')->nullable();\n            $table->text('seo_description')->nullable();\n"
s=s.replace(old,'')
open(p,'w').write(s)
print('done')
