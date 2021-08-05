<?php
declare(strict_types=1);

namespace DigitalRevolution\CodeCoverageInspection\Renderer;

use DigitalRevolution\CodeCoverageInspection\Model\Config\InspectionConfig;
use DigitalRevolution\CodeCoverageInspection\Model\Metric\Failure;
use JsonException;

/**
 * @see https://docs.gitlab.com/ee/user/project/merge_requests/code_quality.html#implementing-a-custom-tool
 */
class GitlabErrorRenderer
{
    /**
     * @param Failure[] $failures
     *
     * @throws JsonException
     */
    public function render(InspectionConfig $config, array $failures): string
    {
        $errors = [];

        foreach ($failures as $failure) {
            $message = RendererHelper::renderReason($config, $failure);

            $error = [
                'description' => $message,
                'fingerprint' => hash(
                    'sha256',
                    implode(
                        [
                            $failure->getMetric()->getFilepath(),
                            $failure->getLineNumber(),
                            $message,
                        ]
                    )
                ),
                'severity'    => 'major',
                'location'    => [
                    'path' => $failure->getMetric()->getFilepath(),
                    'lines' => [
                        'begin' => $failure->getLineNumber(),
                    ],
                ],
            ];

            $errors[] = $error;
        }

        return json_encode($errors, JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT);
    }
}
