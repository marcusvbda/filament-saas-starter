<?php

namespace App\Filament\Widgets;

// use App\Filament\Resources\ContractResource\Widgets\RecentContracts;
use Filament\Widgets\StatsOverviewWidget;
// use Filament\Widgets\StatsOverviewWidget\Stat;

class DashboardMetrics extends StatsOverviewWidget
{
    protected static ?int $sort = 0;

    protected function getStats(): array
    {
        return [
            // Stat::make('Empreendimentos', Enterprise::count())
            //     ->description('Total de entrepreendimentos')
            //     ->icon('heroicon-o-building-office')
            //     ->url(route('filament.admin.resources.enterprises.index'))
            //     ->color('primary'),
            // Stat::make('Clientes', User::where(function ($query) {
            //     $query->whereHas('roles', function ($query) {
            //         $query->where('name', 'cliente');
            //     });
            // })->count())
            //     ->description('Total de clientes')
            //     ->icon('heroicon-o-users')
            //     ->url(route('filament.admin.resources.users.index'))
            //     ->color('primary'),
            // Stat::make('Contratos', Contract::count())
            //     ->description('Total de contratos')
            //     ->icon('heroicon-o-rectangle-stack')
            //     ->url(route('filament.admin.resources.contracts.index'))
            //     ->color('primary'),
        ];
    }
}
