p='database/migrations/2026_10_26_000011_domains_and_tenant_settings.php'
s=open(p).read()
s=s.replace("        Schema::table('tenant_domains', function (Blueprint $table) {\n            $table->dropUnique(['normalized_domain']);\n            $table->dropColumn([", "        Schema::table('tenant_domains', function (Blueprint $table) {\n            $table->dropForeign(['tenant_id']);\n            $table->dropUnique(['normalized_domain']);\n            $table->dropColumn([")
open(p,'w').write(s)
print('done')
