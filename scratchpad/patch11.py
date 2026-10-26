p="database/migrations/2026_10_26_000012_billing_tables.php"
s=open(p).read()
s=s.replace("    public function down(): void\n    {\n", "    public function down(): void\n    {\n        Schema::disableForeignKeyConstraints();\n")
s=s.replace("    }\n};\n", "        Schema::enableForeignKeyConstraints();\n    }\n};\n")
open(p,"w").write(s)
print("done")
