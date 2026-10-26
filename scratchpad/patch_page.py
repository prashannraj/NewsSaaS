p="app/Services/OrganizationService.php"
s=open(p).read()
s=s.replace("                \"tenant_id\" => $tenant->id,\n                \"organization_id\" => $organization->id,\n                \"title\" => \"About\",", "                \"tenant_id\" => $tenant->id,\n                \"title\" => \"About\",")
open(p,"w").write(s)
print("done")
