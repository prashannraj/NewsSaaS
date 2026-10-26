p='database/migrations/2026_10_26_000010_upgrade_cms_tables.php'
s=open(p).read()
s=s.replace("        Schema::table('categories', function (Blueprint $table) {\n            $table->dropForeign(['parent_id']);\n            $table->dropSoftDeletes();", "        Schema::table('categories', function (Blueprint $table) {\n            $table->dropForeign(['parent_id']);\n            $table->dropUnique(['tenant_id', 'slug']);\n            $table->dropSoftDeletes();")
s=s.replace("        Schema::table('tags', function (Blueprint $table) {\n            $table->dropUnique(['tenant_id', 'name']);\n            $table->dropUnique(['tenant_id', 'slug']);", "        Schema::table('tags', function (Blueprint $table) {\n            $table->dropUnique(['tenant_id', 'name']);\n            $table->dropUnique(['tenant_id', 'slug']);")
s=s.replace("        Schema::table('articles', function (Blueprint $table) {\n            $table->dropForeign(['editor_id']);\n            $table->dropSoftDeletes();", "        Schema::table('articles', function (Blueprint $table) {\n            $table->dropForeign(['editor_id']);\n            $table->dropUnique(['tenant_id', 'slug']);\n            $table->dropSoftDeletes();")
open(p,'w').write(s)
print('done')
