<?php declare(strict_types=1);

namespace App\Domains\Server\Action;

use App\Domains\Server\Model\Collection\Server as Collection;
use App\Domains\Server\Model\Server as Model;
use App\Domains\Server\Service\Command\Generator as CommandGenerator;
use App\Services\Command\Artisan;

class StartAll extends ActionAbstract
{
    /**
     * @return void
     */
    public function handle(): void
    {
        $this->iterate();
        $this->sleep();
    }

    /**
     * @return void
     */
    protected function iterate(): void
    {
        foreach ($this->list() as $row) {
            $this->command($row);
        }
    }

    /**
     * @return \App\Domains\Server\Model\Collection\Server
     */
    protected function list(): Collection
    {
        return Model::query()
            ->enabled()
            ->get();
    }

    /**
     * @param \App\Domains\Server\Model\Server $row
     *
     * @return void
     */
    protected function command(Model $row): void
    {
        Artisan::new($this->commandString($row))->exec();
    }

    /**
     * @param \App\Domains\Server\Model\Server $row
     *
     * @return string
     */
    protected function commandString(Model $row): string
    {
        return CommandGenerator::serverStartPort(
            $row->port,
            $this->data['reset'],
            $this->data['debug'] || $row->debug
        );
    }

    /**
     * @return void
     */
    protected function sleep(): void
    {
        sleep(1);
    }
}