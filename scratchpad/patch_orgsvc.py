p="app/Services/OrganizationService.php"
s=open(p).read()
s=s.replace("                \"tenant_id\" => $tenant->id,\n                \"organization_id\" => $organization->id,\n                \"name\" => \"Default\",", "                \"tenant_id\" => $tenant->id,\n                \"name\" => \"Default\",")
s=s.replace("                \"tenant_id\" => $tenant->id,\n                \"organization_id\" => $organization->id,\n                \"settings\" => [\"header\" => [], \"footer\" => []],", "                \"tenant_id\" => $tenant->id,\n                \"settings\" => [\"header\" => [], \"footer\" => []],")
s=s.replace("                \"tenant_id\" => $tenant->id,\n                \"organization_id\" => $organization->id,\n                \"mailer\" => \"smtp\",", "                \"tenant_id\" => $tenant->id,\n                \"mailer\" => \"smtp\",")
open(p,"w").write(s)
print("done")
