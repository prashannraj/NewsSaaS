import re
p='database/migrations/2026_10_26_000010_upgrade_cms_tables.php'
s=open(p).read()
s=s.replace("Schema::table('categories', function (Blueprint $table) {\n            $table->foreignId('parent_id')", "Schema::table('categories', function (Blueprint $table) {\n            $table->dropUnique(['organization_id', 'slug']);\n            $table->unique(['tenant_id', 'slug']);\n            $table->foreignId('parent_id')")
s=s.replace("Schema::table('articles', function (Blueprint $table) {\n            $table->foreignId('editor_id')", "Schema::table('articles', function (Blueprint $table) {\n            $table->dropUnique(['organization_id', 'slug']);\n            $table->unique(['tenant_id', 'slug']);\n            $table->foreignId('editor_id')")
open(p,'w').write(s)
print('done')
