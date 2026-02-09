<?php
declare(strict_types=1);

use Nette\Application\UI\Presenter;
use App\Model\MotoGpService;

class HomepagePresenter extends Presenter
{
    private MotoGpService $motoGpService;

    public function __construct(MotoGpService $motoGpService)
    {
        $this->motoGpService = $motoGpService;
    }

    public function renderDefault(): void
    {
        // Pasamos los datos a la variable $league en Latte
        $this->template->league = $this->motoGpService->getLeagueDetails();
    }
}

?>