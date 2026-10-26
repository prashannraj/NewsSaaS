p='database/migrations/2026_10_26_000010_upgrade_cms_tables.php'
s=open(p).read()
s=s.replace("        Schema::table('categories', function (Blueprint $table) {\n            $table->dropSoftDeletes();\n            $table->dropColumn(['parent_id', 'image_path', 'sort_order', 'is_active']);", "        Schema::table('categories', function (Blueprint $table) {\n            $table->dropForeign(['parent_id']);\n            $table->dropSoftDeletes();\n            $table->dropColumn(['parent_id', 'image_path', 'sort_order', 'is_active']);")
s=s.replace("        Schema::table('articles', function (Blueprint $table) {\n            $table->dropSoftDeletes();\n            $table->dropColumn(['editor_id', 'visibility', 'language_code', 'scheduled_at', 'seo_title', 'seo_description', 'canonical_url', 'allow_indexing']);", "        Schema::table('articles', function (Blueprint $table) {\n            $table->dropForeign(['editor_id']);\n            $table->dropSoftDeletes();\n            $table->dropColumn(['editor_id', 'visibility', 'language_code', 'scheduled_at', 'canonical_url', 'allow_indexing']);")
open(p,'w').write(s)
print('done')
