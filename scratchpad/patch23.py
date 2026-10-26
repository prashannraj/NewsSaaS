p="app/Services/BillingService.php"
s=open(p).read()
old="            foreach ($plan->features as $feature) {\n                Entitlement::updateOrCreate(\n                    ['organization_id' => $organization->id, 'key' => $feature->key],\n                    ['value' => (string) ($feature->pivot->value ?? 'true'), 'source' => 'plan:'.$plan->code]\n                );\n            }\n"
new="            $assignments = $plan->features()->get();\n            if ($assignments->isNotEmpty()) {\n                foreach ($assignments as $feature) {\n                    Entitlement::updateOrCreate(\n                        ['organization_id' => $organization->id, 'key' => $feature->key],\n                        ['value' => (string) ($feature->pivot->value ?? 'true'), 'source' => 'plan:'.$plan->code]\n                    );\n                }\n            } elseif (is_array($plan->features)) {\n                foreach ($plan->features as $key => $value) {\n                    Entitlement::updateOrCreate(\n                        ['organization_id' => $organization->id, 'key' => $key],\n                        ['value' => is_scalar($value) ? (string) $value : json_encode($value), 'source' => 'plan:'.$plan->code]\n                    );\n                }\n            }\n"
s=s.replace(old,new)
open(p,"w").write(s)
print("done")
