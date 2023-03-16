<?php
declare(strict_types=1);

namespace DigitalRevolution\CodeCoverageInspection\Tests\Unit\Lib\Config;

use DigitalRevolution\CodeCoverageInspection\Lib\Config\ConfigFactory;
use DigitalRevolution\CodeCoverageInspection\Lib\Config\ConfigViolation;
use DigitalRevolution\CodeCoverageInspection\Lib\Config\InspectConfig;
use org\bovigo\vfs\vfsStream;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Input\InputInterface;

/**
 * @coversDefaultClass \DigitalRevolution\CodeCoverageInspection\Lib\Config\ConfigFactory
 */
class ConfigFactoryTest extends TestCase
{
    private ConfigFactory $factory;
    private string        $filepath;

    protected function setUp(): void
    {
        $this->filepath = vfsStream::setup()->url() . '/';
        $this->factory  = new ConfigFactory();
    }

    /**
     * @covers ::createInspectConfig
     */
    public function testCreateInspectConfigMinimal(): void
    {
        $configPath   = $this->filepath . 'config.xml';
        $coveragePath = $this->filepath . 'coverage.xml';
        touch($configPath);
        touch($coveragePath);

        $input = $this->createMock(InputInterface::class);
        $input->expects(self::exactly(6))->method('getOption')->willReturn($configPath, null, false, false, false, true);
        $input->expects(self::once())->method('getArgument')->with('coverage')->willReturn($coveragePath);

        $config = $this->factory->createInspectConfig($input);
        static::assertInstanceOf(InspectConfig::class, $config);

        static::assertSame($configPath, $config->getConfigPath()->getPathname());
        static::assertSame($coveragePath, $config->getCoverageFilepath()->getPathname());
        static::assertNull($config->getReportGitlab());
        static::assertNull($config->getReportCheckstyle());
        static::assertSame('php://stdout', $config->getReportText());
        static::assertTrue($config->isExitCodeOnFailure());
    }

    /**
     * @covers ::createInspectConfig
     */
    public function testCreateInspectConfigMultiReport(): void
    {
        $configPath   = $this->filepath . 'config.xml';
        $coveragePath = $this->filepath . 'coverage.xml';
        touch($configPath);
        touch($coveragePath);

        $input = $this->createMock(InputInterface::class);
        $input->expects(self::exactly(6))->method('getOption')->willReturn($configPath, null, 'gitlab.json', 'checkstyle.xml', null, false);
        $input->expects(self::once())->method('getArgument')->with('coverage')->willReturn($coveragePath);

        $config = $this->factory->createInspectConfig($input);
        static::assertInstanceOf(InspectConfig::class, $config);

        static::assertSame($configPath, $config->getConfigPath()->getPathname());
        static::assertSame($coveragePath, $config->getCoverageFilepath()->getPathname());
        static::assertSame('gitlab.json', $config->getReportGitlab());
        static::assertSame('checkstyle.xml', $config->getReportCheckstyle());
        static::assertSame('php://stdout', $config->getReportText());
        static::assertFalse($config->isExitCodeOnFailure());
    }

    /**
     * @covers ::createInspectConfig
     */
    public function testCreateInspectConfigInvalidBaseDir(): void
    {
        $configPath   = $this->filepath . 'config.xml';
        $coveragePath = $this->filepath . 'coverage.xml';
        touch($configPath);
        touch($coveragePath);

        $input = $this->createMock(InputInterface::class);
        $input->expects(self::exactly(2))->method('getOption')->willReturn($configPath, []);
        $input->expects(self::once())->method('getArgument')->with('coverage')->willReturn($coveragePath);

        $config = $this->factory->createInspectConfig($input);
        static::assertEquals(new ConfigViolation('--base-dir expecting a value string as argument'), $config);
    }

    /**
     * @covers ::createInspectConfig
     */
    public function testCreateInspectConfigInvalidGitlabReport(): void
    {
        $configPath   = $this->filepath . 'config.xml';
        $coveragePath = $this->filepath . 'coverage.xml';
        touch($configPath);
        touch($coveragePath);

        $input = $this->createMock(InputInterface::class);
        $input->expects(self::exactly(5))->method('getOption')->willReturn($configPath, null, [], false, false);
        $input->expects(self::once())->method('getArgument')->with('coverage')->willReturn($coveragePath);

        $config = $this->factory->createInspectConfig($input);
        static::assertEquals(new ConfigViolation('--reportGitlab expecting the value to absent or string argument'), $config);
    }

    /**
     * @covers ::createInspectConfig
     */
    public function testCreateInspectConfigInvalidCheckstyleReport(): void
    {
        $configPath   = $this->filepath . 'config.xml';
        $coveragePath = $this->filepath . 'coverage.xml';
        touch($configPath);
        touch($coveragePath);

        $input = $this->createMock(InputInterface::class);
        $input->expects(self::exactly(5))->method('getOption')->willReturn($configPath, null, false, [], false);
        $input->expects(self::once())->method('getArgument')->with('coverage')->willReturn($coveragePath);

        $config = $this->factory->createInspectConfig($input);
        static::assertEquals(new ConfigViolation('--reportCheckstyle expecting the value to absent or string argument'), $config);
    }

    /**
     * @covers ::createInspectConfig
     */
    public function testCreateInspectConfigInvalidTextReport(): void
    {
        $configPath   = $this->filepath . 'config.xml';
        $coveragePath = $this->filepath . 'coverage.xml';
        touch($configPath);
        touch($coveragePath);

        $input = $this->createMock(InputInterface::class);
        $input->expects(self::exactly(5))->method('getOption')->willReturn($configPath, null, false, false, []);
        $input->expects(self::once())->method('getArgument')->with('coverage')->willReturn($coveragePath);

        $config = $this->factory->createInspectConfig($input);
        static::assertEquals(new ConfigViolation('--reportText expecting the value to absent or string argument'), $config);
    }

    /**
     * @covers ::createInspectConfig
     */
    public function testCreateInspectConfigDuplicateOutputReport(): void
    {
        $configPath   = $this->filepath . 'config.xml';
        $coveragePath = $this->filepath . 'coverage.xml';
        touch($configPath);
        touch($coveragePath);

        $input = $this->createMock(InputInterface::class);
        $input->expects(self::exactly(5))->method('getOption')->willReturn($configPath, null, null, null);
        $input->expects(self::once())->method('getArgument')->with('coverage')->willReturn($coveragePath);

        $config = $this->factory->createInspectConfig($input);
        static::assertEquals(new ConfigViolation('Two or more reports output to the same destination'), $config);
    }
}
