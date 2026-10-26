p='database/migrations/2026_10_26_000001_upgrade_users_and_organizations.php'
s=open(p).read()
s=s.replace("        Schema::table('organizations', function (Blueprint $table) {\n            $table->dropSoftDeletes();\n            $table->dropColumn([", "        Schema::table('organizations', function (Blueprint $table) {\n            $table->dropUnique(['public_id']);\n            $table->dropSoftDeletes();\n            $table->dropColumn([")
s=s.replace("        Schema::table('users', function (Blueprint $table) {\n            $table->dropSoftDeletes();\n            $table->dropColumn([", "        Schema::table('users', function (Blueprint $table) {\n            $table->dropUnique(['public_id']);\n            $table->dropSoftDeletes();\n            $table->dropColumn([")
open(p,'w').write(s)
print('done')
