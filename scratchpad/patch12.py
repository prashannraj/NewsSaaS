p="database/migrations/2026_10_26_000010_upgrade_cms_tables.php"
s=open(p).read()
s=s.replace("    public function down(): void\n    {\n", "    public function down(): void\n    {\n        Schema::disableForeignKeyConstraints();\n")
# enable before final closing of down method: replace last "    }\n};\n"
idx=s.rfind("    }\n};\n")
s=s[:idx]+"        Schema::enableForeignKeyConstraints();\n    }\n};\n"
open(p,"w").write(s)
print("done")
