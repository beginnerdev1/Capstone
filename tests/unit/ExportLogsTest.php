<?php

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\FeatureTestTrait;
use CodeIgniter\Test\DatabaseTestTrait;
use App\Models\AdminActivityLogModel;

/**
 * @internal
 */
final class ExportLogsTest extends CIUnitTestCase
{
    use FeatureTestTrait;
    use DatabaseTestTrait;

    public function testExportUsesDetailsFallback()
    {
        $model = new AdminActivityLogModel();
        $now = date('Y-m-d H:i:s');

        // details contains an array of actions; include a resource inside the last action
        $details = json_encode([
            [
                'action' => 'create',
                'route'  => '/users',
                'method' => 'POST',
                'resource' => 'user/123',
                'details' => null,
                'time'   => $now,
            ]
        ]);

        // Insert a row with empty top-level resource but details including the resource
        $insertId = $model->insert([
            'actor_type' => 'admin',
            'actor_id'   => 9999,
            'action'     => 'test_export',
            'route'      => '/test/export',
            'method'     => 'POST',
            'resource'   => '',
            'details'    => $details,
            'ip_address' => '127.0.0.1',
            'user_agent' => 'phpunit',
            'created_at' => $now,
        ], true);

        // Call the export endpoint and assert the CSV body contains the resource from details
        $response = $this->get('superadmin/exportLogs');
        $this->assertTrue($response->isOK(), 'exportLogs did not return 200 OK');

        $body = (string) $response->getBody();
        $this->assertStringContainsString('user/123', $body, 'Exported CSV should include resource from details fallback');
    }
}
