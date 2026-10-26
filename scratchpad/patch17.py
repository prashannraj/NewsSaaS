p="database/migrations/0001_01_01_000000_create_users_table.php"
s=open(p).read()
s=s.replace("    public function down(): void\n    {\n", "    public function down(): void\n    {\n        Schema::disableForeignKeyConstraints();\n")
idx=s.rfind("    }\n};\n")
s=s[:idx]+"        Schema::enableForeignKeyConstraints();\n    }\n};\n"
open(p,"w").write(s)
print("done")
