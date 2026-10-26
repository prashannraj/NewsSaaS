p="database/migrations/2026_10_26_000001_upgrade_users_and_organizations.php"
s=open(p).read()
s=s.replace("    public function down(): void\n    {\n", "    public function down(): void\n    {\n        Schema::disableForeignKeyConstraints();\n")
idx=s.rfind("    }\n};\n")
s=s[:idx]+"        Schema::enableForeignKeyConstraints();\n    }\n};\n"
open(p,"w").write(s)
print("done")
