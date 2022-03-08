<?php

declare(strict_types=1);

namespace App\UseCases\Admin\Manage\Data\Tools;

use App\Entities\Data\Tool;
use App\Repositories\Data\ToolRepository;

/**
 * Class ToolService
 * Manage tools.
 *
 * @package App\UseCases\Admin\Manage\Data\Tools
 */
class ToolService
{
    /**
     * @var ToolRepository
     */
    private $toolRepository;

    /**
     * ToolService constructor.
     * @param ToolRepository $toolRepository
     */
    public function __construct(ToolRepository $toolRepository)
    {
        $this->toolRepository = $toolRepository;
    }

    /**
     * @param string $title
     * @return Tool
     */
    public function create(string $title): Tool
    {
        try {
            $tool = Tool::createRegular($title);
            $tool->saveOrFail();
        } catch (\Throwable $e) {
            throw new \DomainException('Creating error');
        }
        return $tool;
    }

    /**
     * @param Tool $tool
     * @param string $title
     */
    public function edit(Tool $tool, string $title): void
    {
        $tool =  $this->toolRepository->getById($tool->id);
        try {
            $tool->update([
                'title' => $title,
            ]);
        } catch (\Exception $e) {
            throw new \DomainException('Updating error');
        }
    }

    /**
     * @param Tool $tool
     */
    public function destroy(Tool $tool): void
    {
        $tool =  $this->toolRepository->getById($tool->id);
        try {
            $tool->delete();
        } catch (\Exception $e) {
            throw new \DomainException('Deleting error');
        }
    }
}
