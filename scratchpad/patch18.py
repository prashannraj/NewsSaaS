p="app/Models/TenantDomain.php"
s=open(p).read()
s=s.replace("use Illuminate\\Database\\Eloquent\\SoftDeletes;\n", "")
s=s.replace("use HasFactory, HasTenant, SoftDeletes;", "use HasFactory, HasTenant;")
open(p,"w").write(s)
print("done")
