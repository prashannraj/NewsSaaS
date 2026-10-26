p="app/Services/BillingService.php"
s=open(p).read()
s=s.replace("                'amount_cents' => $plan->price_cents,\n                'amount' => $plan->price_cents,\n", "                'amount_cents' => $plan->price_cents,\n")
open(p,"w").write(s)
print("done")
