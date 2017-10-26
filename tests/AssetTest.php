<?php

use Illuminate\Container\Container;
use PHPUnit\Framework\TestCase;
use Malyusha\WebpackAssets\Asset;

class AssetTest extends TestCase
{
    protected $file;

    /**
     * @var \Malyusha\WebpackAssets\Asset
     */
    protected $asset;

    /**
     * @var \Mockery\MockInterface
     */
    protected $urlMock;

    public function setUp()
    {
        parent::setUp();

        $app = new Container();
        Container::setInstance($app);

        $this->urlMock = Mockery::mock(\Illuminate\Contracts\Routing\UrlGenerator::class);
        $this->urlMock->shouldReceive('asset')->andReturn('http://site.com');
        $appMock = Mockery::mock(\Illuminate\Contracts\Foundation\Application::class);
        $appMock->shouldReceive('basePath')->once()->withAnyArgs()->andReturnUsing(function ($path
        ) {
            return __DIR__ . '/' . $path;
        });

        $this->file = __DIR__ . '/fixtures/assets.json';
        $this->asset = new Asset($this->file, $appMock, $this->urlMock);
    }

    /**
     * @covers Asset::assets()
     */
    public function test_it_reads_json_with_assets()
    {
        $content = $this->getJson();

        $this->assertSame($content, $this->asset->assets());
    }

    /**
     * @covers Asset::searchExtension()
     */
    public function test_it_returns_correct_file_relative_path()
    {
        $this->assertEquals($this->asset->path('main.js'), 'assets/main.js');
    }

    /**
     * @covers Asset::path()
     */
    public function test_it_returns_raw_file_content()
    {
        $this->assertEquals($this->asset->content('main.css'), $this->getFileContent('css/main.css'));
    }

    /**
     * @covers Asset::path()
     */
    public function test_it_returns_correct_file_absolute_path()
    {
        $this->assertEquals($this->asset->path('main.js', true), __DIR__ . '/public/assets/main.js');
    }

    /**
     * @covers Asset::rawStyle()
     */
    public function test_it_returns_raw_style_node()
    {
        $content = $this->getFileContent('css/main.css');

        $this->assertEquals("<style>{$content}</style>", $this->asset->rawStyle('main.css'));
    }

    /**
     * @covers Asset::rawScript()
     */
    public function test_it_returns_raw_script_node()
    {
        $content = $this->getFileContent('main.js');

        $this->assertEquals("<script type=\"text/javascript\">{$content}</script>", $this->asset->rawScript('main.js'));
    }

    protected function getFileContent($file)
    {
        return file_get_contents(__DIR__ . '/public/assets/' . $file);
    }

    protected function getJson()
    {
        return json_decode(file_get_contents($this->file), true);
    }
}