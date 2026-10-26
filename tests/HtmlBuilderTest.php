<?php

namespace Tests;

use Souravmsh\Html\Services\HtmlBuilder;

class HtmlBuilderTest extends TestCase
{
    protected HtmlBuilder $html;

    public function setUp(): void
    {
        $this->html = new HtmlBuilder();
    }

    public function testEntitiesAndDecode(): void
    {
        $encoded = $this->html->entities('foo & "bar" <baz>');
        $this->assertEquals('foo &amp; &quot;bar&quot; &lt;baz&gt;', $encoded);

        $decoded = $this->html->decode($encoded);
        $this->assertEquals('foo & "bar" <baz>', $decoded);
    }

    public function testScript(): void
    {
        $result = (string) $this->html->script('js/app.js', ['defer' => true]);
        $this->assertEquals('<script src="https://example.com/asset/js/app.js" defer></script>', $result);
    }

    public function testStyle(): void
    {
        $result = (string) $this->html->style('css/app.css');
        $this->assertEquals('<link media="all" type="text/css" rel="stylesheet" href="https://example.com/asset/css/app.css">', $result);
    }

    public function testImage(): void
    {
        $result = (string) $this->html->image('img/profile.jpg', 'Avatar', ['class' => ['rounded', 'shadow']]);
        $this->assertEquals('<img src="https://example.com/asset/img/profile.jpg" alt="Avatar" class="rounded shadow">', $result);
    }

    public function testFavicon(): void
    {
        $result = (string) $this->html->favicon('favicon.ico');
        $this->assertEquals('<link rel="shortcut icon" type="image/x-icon" href="https://example.com/asset/favicon.ico">', $result);
    }

    public function testLink(): void
    {
        $result = (string) $this->html->link('user/profile', 'My Profile', ['class' => 'btn btn-primary']);
        $this->assertEquals('<a href="https://example.com/user/profile" class="btn btn-primary">My Profile</a>', $result);
    }

    public function testSecureLink(): void
    {
        $result = (string) $this->html->secureLink('checkout', 'Checkout');
        $this->assertEquals('<a href="https://example.com/checkout">Checkout</a>', $result);
    }

    public function testLinkAsset(): void
    {
        $result = (string) $this->html->linkAsset('files/doc.pdf', 'Download PDF');
        $this->assertEquals('<a href="https://example.com/asset/files/doc.pdf">Download PDF</a>', $result);
    }

    public function testLinkRoute(): void
    {
        $result = (string) $this->html->linkRoute('users.show', 'View User', ['id' => 10]);
        $this->assertEquals('<a href="https://example.com/route/users.show">View User</a>', $result);
    }

    public function testLinkAction(): void
    {
        $result = (string) $this->html->linkAction('UserController@index', 'Users');
        $this->assertEquals('<a href="https://example.com/action/UserController@index">Users</a>', $result);
    }

    public function testMailto(): void
    {
        $result = (string) $this->html->mailto('hello@example.com', 'Contact Us');
        $this->assertStringContains('href="', $result);
        $this->assertStringContains('Contact Us</a>', $result);
        $this->assertTrue(str_contains($result, '&#64;') || str_contains($result, '&#x40;'));
    }

    public function testNbsp(): void
    {
        $this->assertEquals('&nbsp;', $this->html->nbsp());
        $this->assertEquals('&nbsp;&nbsp;&nbsp;', $this->html->nbsp(3));
    }

    public function testLists(): void
    {
        $ul = (string) $this->html->ul(['First', 'Second', 'Nested' => ['Sub 1', 'Sub 2']]);
        $this->assertEquals('<ul><li>First</li><li>Second</li><li>Nested<ul><li>Sub 1</li><li>Sub 2</li></ul></li></ul>', $ul);

        $ol = (string) $this->html->ol(['Step 1', 'Step 2'], ['class' => 'steps']);
        $this->assertEquals('<ol class="steps"><li>Step 1</li><li>Step 2</li></ol>', $ol);

        $dl = (string) $this->html->dl(['Name' => 'Shohrab', 'Role' => ['Developer', 'Author']]);
        $this->assertEquals('<dl><dt>Name</dt><dd>Shohrab</dd><dt>Role</dt><dd>Developer</dd><dd>Author</dd></dl>', $dl);
    }

    public function testMetaAndTag(): void
    {
        $meta = (string) $this->html->meta('description', 'Awesome Laravel Form Package');
        $this->assertEquals('<meta name="description" content="Awesome Laravel Form Package">', $meta);

        $tag = (string) $this->html->tag('span', 'Active', ['class' => 'badge bg-success']);
        $this->assertEquals('<span class="badge bg-success">Active</span>', $tag);
    }

    public function testAttributes(): void
    {
        $attrs = $this->html->attributes([
            'required',
            'disabled' => false,
            'readonly' => true,
            'class'    => ['btn', 'btn-sm'],
            'data-id'  => 42,
        ]);

        $this->assertEquals(' required readonly class="btn btn-sm" data-id="42"', $attrs);
    }

    public function testMacros(): void
    {
        HtmlBuilder::macro('customBadge', function (string $text) {
            return '<span class="custom-badge">' . $text . '</span>';
        });

        $badge = $this->html->customBadge('VIP');
        $this->assertEquals('<span class="custom-badge">VIP</span>', $badge);
    }

    public function testHtmlHelperFunction(): void
    {
        $this->assertTrue(function_exists('html'));
        $instance = html();
        $this->assertTrue($instance instanceof HtmlBuilder);
        $link = (string) html()->link('about', 'About Us');
        $this->assertEquals('<a href="https://example.com/about">About Us</a>', $link);
    }
}
