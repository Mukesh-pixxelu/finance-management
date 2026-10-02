<header class="top">
    <p class="brand"><x-icon name="wallet" /> Ledger</p>
    <nav class="nav">
        <a href="{{ route('dashboard') }}" @class(['nav-link', 'is-current' => request()->routeIs('dashboard')])><x-icon name="receipt" /> Transactions</a>
        <a href="{{ route('savings.index') }}" @class(['nav-link', 'is-current' => request()->routeIs('savings.*')])><x-icon name="piggy" /> Savings</a>
        <a href="{{ route('account.edit') }}" @class(['nav-link', 'is-current' => request()->routeIs('account.*')])><x-icon name="user" /> Account</a>
    </nav>
    <form class="logout" method="POST" action="{{ route('logout') }}">
        @csrf
        <button class="button-quiet" type="submit"><x-icon name="logout" /> Logout</button>
    </form>
</header>
