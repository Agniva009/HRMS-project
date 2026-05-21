<?php

namespace Tests\Unit;

use App\Http\Middleware\UppercaseInput;
use Illuminate\Http\Request;
use PHPUnit\Framework\TestCase;

class UppercaseInputMiddlewareTest extends TestCase
{
    private UppercaseInput $middleware;

    protected function setUp(): void
    {
        parent::setUp();
        $this->middleware = new UppercaseInput();

        // Minimal config stub so the middleware can read exception fields
        // without booting the full Laravel app.
        if (! function_exists('config')) {
            // Already defined by Laravel; this branch only runs in
            // isolation (pure PHPUnit without the framework).
        }
    }

    /** @test */
    public function it_uppercases_regular_fields(): void
    {
        $request = $this->makeRequest([
            'name'       => 'john doe',
            'department' => 'engineering',
        ]);

        $this->handleMiddleware($request);

        $this->assertSame('JOHN DOE', $request->input('name'));
        $this->assertSame('ENGINEERING', $request->input('department'));
    }

    /** @test */
    public function it_skips_exception_fields(): void
    {
        $request = $this->makeRequest([
            'email'    => 'User@Example.com',
            'password' => 'Secret123!',
            'city'     => 'mumbai',
        ]);

        $this->handleMiddleware($request);

        $this->assertSame('User@Example.com', $request->input('email'));
        $this->assertSame('Secret123!', $request->input('password'));
        $this->assertSame('MUMBAI', $request->input('city'));
    }

    /** @test */
    public function it_handles_nested_data(): void
    {
        $request = $this->makeRequest([
            'employee' => [
                'name'  => 'jane smith',
                'email' => 'jane@corp.com',
            ],
        ]);

        $this->handleMiddleware($request);

        $this->assertSame('JANE SMITH', $request->input('employee.name'));
        $this->assertSame('jane@corp.com', $request->input('employee.email'));
    }

    /** @test */
    public function it_handles_empty_request(): void
    {
        $request = $this->makeRequest([]);
        $this->handleMiddleware($request);
        $this->assertEmpty($request->all());
    }

    /** @test */
    public function it_preserves_non_string_values(): void
    {
        $request = $this->makeRequest([
            'name'   => 'john',
            'age'    => 30,
            'active' => true,
        ]);

        $this->handleMiddleware($request);

        $this->assertSame('JOHN', $request->input('name'));
        $this->assertSame(30, $request->input('age'));
        $this->assertTrue($request->input('active'));
    }

    /* --------------------------------------------------------- */

    private function makeRequest(array $data): Request
    {
        return new Request([], $data);
    }

    private function handleMiddleware(Request $request): void
    {
        // Stub config() to return our exception list
        app()->bind('config', function () {
            return new class {
                public function get(string $key, $default = null)
                {
                    if ($key === 'uppercase.except') {
                        return [
                            'email', 'email_address', 'username', 'user_name',
                            'password', 'password_confirmation', 'url', 'website',
                            'link', 'callback_url', 'redirect_uri', 'system_id',
                            'token', 'api_key', '_token', '_method',
                        ];
                    }
                    return $default;
                }
            };
        });

        $this->middleware->handle($request, function ($req) {
            return response('OK');
        });
    }
}
