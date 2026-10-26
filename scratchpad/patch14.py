p="database/migrations/2026_10_26_000009_add_tenant_id_to_existing_tables.php"
s=open(p).read()
s=s.replace("    public function down(): void\n    {\n", "    public function down(): void\n    {\n        Schema::disableForeignKeyConstraints();\n")
idx=s.rfind("    }\n};\n")
s=s[:idx]+"        Schema::enableForeignKeyConstraints();\n    }\n};\n"
open(p,"w").write(s)
print("done")
