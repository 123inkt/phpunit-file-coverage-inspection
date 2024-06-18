<?php
declare(strict_types=1);

namespace DigitalRevolution\CodeCoverageInspection\Tests\Unit\Lib\Config;

use DigitalRevolution\CodeCoverageInspection\Lib\Config\ConfigFactory;
use DigitalRevolution\CodeCoverageInspection\Lib\Config\ConfigViolation;
use DigitalRevolution\CodeCoverageInspection\Lib\Config\InspectConfig;
use org\bovigo\vfs\vfsStream;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Input\InputInterface;

#[CoversClass(ConfigFactory::class)]
class ConfigFactoryTest extends TestCase
{
    private ConfigFactory $factory;
    private string $filepath;

    protected function setUp(): void
    {
        $this->filepath = vfsStream::setup()->url() . '/';
        $this->factory  = new ConfigFactory();
    }

    public function testCreateInspectConfigMinimal(): void
    {
        $configPath   = $this->filepath . 'config.xml';
        $coveragePath = $this->filepath . 'coverage.xml';
        touch($configPath);
        touch($coveragePath);

        $input = $this->createMock(InputInterface::class);
        $input->expects(self::exactly(6))->method('getOption')->willReturn($configPath, null, false, false, false, true);
        $input->expects(self::once())->method('getArgument')->with('coverage')->willReturn([$coveragePath]);

        $config = $this->factory->createInspectConfig($input);
        static::assertInstanceOf(InspectConfig::class, $config);

        static::assertSame($configPath, $config->getConfigPath()->getPathname());
        static::assertSame($coveragePath, $config->getCoveragesFilepath()[0]->getPathname());
        static::assertNull($config->getReportGitlab());
        static::assertNull($config->getReportCheckstyle());
        static::assertSame('php://stdout', $config->getReportText());
        static::assertTrue($config->isExitCodeOnFailure());
    }

    public function testCreateInspectConfigMultiReport(): void
    {
        $configPath   = $this->filepath . 'config.xml';
        $coveragePath = $this->filepath . 'coverage.xml';
        touch($configPath);
        touch($coveragePath);

        $input = $this->createMock(InputInterface::class);
        $input->expects(self::exactly(6))->method('getOption')->willReturn($configPath, null, 'gitlab.json', 'checkstyle.xml', null, false);
        $input->expects(self::once())->method('getArgument')->with('coverage')->willReturn([$coveragePath]);

        $config = $this->factory->createInspectConfig($input);
        static::assertInstanceOf(InspectConfig::class, $config);

        static::assertSame($configPath, $config->getConfigPath()->getPathname());
        static::assertSame($coveragePath, $config->getCoveragesFilepath()[0]->getPathname());
        static::assertSame('gitlab.json', $config->getReportGitlab());
        static::assertSame('checkstyle.xml', $config->getReportCheckstyle());
        static::assertSame('php://stdout', $config->getReportText());
        static::assertFalse($config->isExitCodeOnFailure());
    }

    public function testCreateInspectConfigInvalidCoverage(): void
    {
        $configPath   = $this->filepath . 'config.xml';
        $coveragePath = $this->filepath . 'coverage.xml';
        touch($configPath);
        touch($coveragePath);

        $input = $this->createMock(InputInterface::class);
        $input->expects(self::once())->method('getOption')->willReturn($configPath, []);
        $input->expects(self::once())->method('getArgument')->with('coverage')->willReturn($coveragePath);

        $config = $this->factory->createInspectConfig($input);
        static::assertEquals(new ConfigViolation('Coverage argument should be an array'), $config);
    }

    public function testCreateInspectConfigInvalidBaseDir(): void
    {
        $configPath   = $this->filepath . 'config.xml';
        $coveragePath = $this->filepath . 'coverage.xml';
        touch($configPath);
        touch($coveragePath);

        $input = $this->createMock(InputInterface::class);
        $input->expects(self::exactly(2))->method('getOption')->willReturn($configPath, []);
        $input->expects(self::once())->method('getArgument')->with('coverage')->willReturn([$coveragePath]);

        $config = $this->factory->createInspectConfig($input);
        static::assertEquals(new ConfigViolation('--base-dir expecting a value string as argument'), $config);
    }

    public function testCreateInspectConfigInvalidGitlabReport(): void
    {
        $configPath   = $this->filepath . 'config.xml';
        $coveragePath = $this->filepath . 'coverage.xml';
        touch($configPath);
        touch($coveragePath);

        $input = $this->createMock(InputInterface::class);
        $input->expects(self::exactly(5))->method('getOption')->willReturn($configPath, null, [], false, false);
        $input->expects(self::once())->method('getArgument')->with('coverage')->willReturn([$coveragePath]);

        $config = $this->factory->createInspectConfig($input);
        static::assertEquals(new ConfigViolation('--reportGitlab expecting the value to absent or string argument'), $config);
    }

    public function testCreateInspectConfigInvalidCheckstyleReport(): void
    {
        $configPath   = $this->filepath . 'config.xml';
        $coveragePath = $this->filepath . 'coverage.xml';
        touch($configPath);
        touch($coveragePath);

        $input = $this->createMock(InputInterface::class);
        $input->expects(self::exactly(5))->method('getOption')->willReturn($configPath, null, false, [], false);
        $input->expects(self::once())->method('getArgument')->with('coverage')->willReturn([$coveragePath]);

        $config = $this->factory->createInspectConfig($input);
        static::assertEquals(new ConfigViolation('--reportCheckstyle expecting the value to absent or string argument'), $config);
    }

    public function testCreateInspectConfigInvalidTextReport(): void
    {
        $configPath   = $this->filepath . 'config.xml';
        $coveragePath = $this->filepath . 'coverage.xml';
        touch($configPath);
        touch($coveragePath);

        $input = $this->createMock(InputInterface::class);
        $input->expects(self::exactly(5))->method('getOption')->willReturn($configPath, null, false, false, []);
        $input->expects(self::once())->method('getArgument')->with('coverage')->willReturn([$coveragePath]);

        $config = $this->factory->createInspectConfig($input);
        static::assertEquals(new ConfigViolation('--reportText expecting the value to absent or string argument'), $config);
    }

    public function testCreateInspectConfigDuplicateOutputReport(): void
    {
        $configPath   = $this->filepath . 'config.xml';
        $coveragePath = $this->filepath . 'coverage.xml';
        touch($configPath);
        touch($coveragePath);

        $input = $this->createMock(InputInterface::class);
        $input->expects(self::exactly(5))->method('getOption')->willReturn($configPath, null, null, null);
        $input->expects(self::once())->method('getArgument')->with('coverage')->willReturn([$coveragePath]);

        $config = $this->factory->createInspectConfig($input);
        static::assertEquals(new ConfigViolation('Two or more reports output to the same destination'), $config);
    }
}
