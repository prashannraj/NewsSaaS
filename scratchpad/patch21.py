p="database/migrations/2026_10_26_000012_billing_tables.php"
s=open(p).read()
s=s.replace("            $table->bigInteger('unit_amount')->nullable()->after('quantity');", "            $table->char('currency', 3)->default('USD')->after('quantity');\n            $table->bigInteger('unit_amount')->nullable()->after('currency');")
open(p,"w").write(s)
print("done")
