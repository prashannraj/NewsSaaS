<?php

use Illuminate\Support\Facades\Schema;

it('has core tenant tables', function () {
    foreach (['organizations', 'tenant_domains', 'plans', 'subscriptions', 'billing_invoices', 'payments', 'entitlements', 'articles', 'tags', 'article_tag'] as $table) {
        expect(Schema::hasTable($table))->toBeTrue();
    }
});
