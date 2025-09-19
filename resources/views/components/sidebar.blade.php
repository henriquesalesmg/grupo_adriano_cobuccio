<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">
    <!-- Sidebar - Brand -->
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="{{ route('dashboard') }}">
        <div class="sidebar-brand-icon rotate-n-15">
            <i class="fas fa-wallet"></i>
        </div>
        <div class="sidebar-brand-text mx-3">Carteira</div>
    </a>
    <!-- Divider -->
    <hr class="sidebar-divider my-0">
    <!-- Nav Item - Dashboard -->
    <li class="nav-item @if (request()->routeIs('dashboard')) active @endif">
        <a class="nav-link" href="{{ route('dashboard') }}">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span>Dashboard</span></a>
    </li>
    <!-- Heading -->
    <div class="sidebar-heading">
        Movimentações
    </div>
    <!-- Nav Item - Movimentações -->
    <li class="nav-item @if (request()->routeIs('transactions')) active @endif">
        <a class="nav-link" href="{{ route('transactions') }}">
            <i class="fas fa-fw fa-exchange-alt"></i>
            <span>Movimentações</span>
        </a>
    </li>
    <!-- Divider -->
    <hr class="sidebar-divider">
    <li class="nav-item" @if (request()->routeIs(patterns: 'historical.index')) active @endif>
        <a class="nav-link" href="{{ route('historical.index') }}">
            <i class="fas fa-fw fa-exchange-alt"></i>
            <span>Histórico de Transferências</span></a>
    </li>
    <li class="nav-item" @if (request()->routeIs(patterns: 'reversals.index')) active @endif>
        <a class="nav-link" href="{{ route('reversals.index') }}">
            <i class="fas fa-fw fa-undo"></i>
            <span>Reversões</span></a>
    </li>
    <li class="nav-item" @if (request()->routeIs(patterns: 'activities.index')) active @endif>
        <a class="nav-link" href="{{ route('activities.index') }}">
            <i class="fas fa-fw fa-user-astronaut"></i>
            <span>Atividades no sistema</span></a>
    </li>
    <!-- Divider -->
    <hr class="sidebar-divider d-none d-md-block">
</ul>
