<div class="flex flex-col antialiased bg-black text-white">
    <div class="fixed flex flex-col top-0 left-0 w-64 bg-gray-900 h-full border-r border-gray-800 shadow-lg">
<!-- Logo -->
<div class="flex items-center justify-center h-42 border-b border-gray-800 mb-4 pt-4 pb-4">
    <img src="{{ asset('storage/img/gs2.png') }}" alt="GS Barbearia" class="w-32 h-auto">
</div>

        <!-- Sidebar -->
        <div class="overflow-y-auto flex-grow">
            <ul class="flex flex-col py-4 space-y-1">

                <!-- Menu Title -->
                <li class="px-5">
                    <div class="flex flex-row items-center h-8">
                        <div class="text-sm font-light tracking-wide text-gray-400 uppercase">Menu</div>
                    </div>
                </li>

                <!-- Dashboard -->
                <li>
                    <a href="{{ route('dashboard') }}" 
                    class="relative flex flex-row items-center h-12 focus:outline-none px-6 transition
                    {{ request()->routeIs('dashboard') ? 'bg-gray-800 text-yellow-400 border-l-4 border-yellow-400' : 'text-gray-300 hover:text-yellow-400 border-l-4 border-transparent hover:border-yellow-400 hover:bg-gray-800' }}">
                        <i class="fas fa-tachometer-alt w-5 h-5"></i>
                        <span class="ml-3 text-sm font-semibold">Dashboard</span>
                    </a>
                </li>

                <!-- Gerenciar Horários -->
                <li>
                    <a href="{{ route('admin.schedules.index') }}" 
                    class="relative flex flex-row items-center h-12 focus:outline-none px-6 transition
                    {{ request()->routeIs('admin.schedules.index') ? 'bg-gray-800 text-yellow-400 border-l-4 border-yellow-400' : 'text-gray-300 hover:text-yellow-400 border-l-4 border-transparent hover:border-yellow-400 hover:bg-gray-800' }}">
                        <i class="fas fa-calendar-check w-5 h-5"></i>
                        <span class="ml-3 text-sm font-semibold">Gerenciar Horários</span>
                    </a>
                </li>

                                <!-- Financeiro -->

                <li>
                    <a href="{{ route('admin.payments.index') }}" class="relative flex flex-row items-center h-12 focus:outline-none hover:bg-gray-800 text-gray-300 hover:text-yellow-400 border-l-4 border-transparent hover:border-yellow-400 px-6 transition">
                        <i class="fas fa-chart-line w-5 h-5"></i>
                        <span class="ml-3 text-sm font-semibold">Financeiro</span>
                    </a>
                </li>




                <!-- Criar Serviço -->
                <li>
                    <a href="{{ route('services.create') }}" 
                    class="relative flex flex-row items-center h-12 focus:outline-none px-6 transition
                    {{ request()->routeIs('services.create') ? 'bg-gray-800 text-yellow-400 border-l-4 border-yellow-400' : 'text-gray-300 hover:text-yellow-400 border-l-4 border-transparent hover:border-yellow-400 hover:bg-gray-800' }}">
                        <i class="fas fa-cut w-5 h-5"></i>
                        <span class="ml-3 text-sm font-semibold">Criar Serviço</span>
                    </a>
                </li>

                <!-- Ver Serviços -->
                <li>
                    <a href="{{ route('services.index') }}" 
                    class="relative flex flex-row items-center h-12 focus:outline-none px-6 transition
                    {{ request()->routeIs('services.index') ? 'bg-gray-800 text-yellow-400 border-l-4 border-yellow-400' : 'text-gray-300 hover:text-yellow-400 border-l-4 border-transparent hover:border-yellow-400 hover:bg-gray-800' }}">
                        <i class="fas fa-list w-5 h-5"></i>
                        <span class="ml-3 text-sm font-semibold">Ver Serviços</span>
                    </a>
                </li>

              <!-- Cadastrar Horário de Almoço -->
                <li>
                    <a href="{{ route('lunch-break.create') }}" 
                    class="relative flex flex-row items-center h-12 focus:outline-none px-6 transition
                    {{ request()->routeIs('lunch-break.create') ? 'bg-gray-800 text-yellow-400 border-l-4 border-yellow-400' : 'text-gray-300 hover:text-yellow-400 border-l-4 border-transparent hover:border-yellow-400 hover:bg-gray-800' }}">
                        <i class="fas fa-utensils w-5 h-5"></i>
                        <span class="ml-3 text-sm font-semibold">Horário de Almoço</span>
                    </a>
                </li>

<!-- Criar Horários -->
<li>
    <a href="{{ route('schedules.create') }}" 
    class="relative flex flex-row items-center h-12 focus:outline-none px-6 transition
    {{ request()->routeIs('schedules.create') ? 'bg-gray-800 text-yellow-400 border-l-4 border-yellow-400' : 'text-gray-300 hover:text-yellow-400 border-l-4 border-transparent hover:border-yellow-400 hover:bg-gray-800' }}">
        <i class="fas fa-calendar-plus w-5 h-5"></i>
        <span class="ml-3 text-sm font-semibold">Criar Horários</span>
    </a>
</li>

<!-- Excluir Horários -->
<li>
    <a href="{{ route('schedules.delete') }}" 
    class="relative flex flex-row items-center h-12 focus:outline-none px-6 transition
    {{ request()->routeIs('schedules.delete') ? 'bg-gray-800 text-yellow-400 border-l-4 border-yellow-400' : 'text-gray-300 hover:text-yellow-400 border-l-4 border-transparent hover:border-yellow-400 hover:bg-gray-800' }}">
        <i class="fas fa-trash-alt w-5 h-5"></i>
        <span class="ml-3 text-sm font-semibold">Excluir Horários</span>
    </a>
</li>

<!-- Horários Fixos -->
<li>
    <a href="{{ route('schedules.fixed.index') }}" 
    class="relative flex flex-row items-center h-12 focus:outline-none px-6 transition
    {{ request()->routeIs('schedules.fixed.index') ? 'bg-gray-800 text-yellow-400 border-l-4 border-yellow-400' : 'text-gray-300 hover:text-yellow-400 border-l-4 border-transparent hover:border-yellow-400 hover:bg-gray-800' }}">
        <i class="fas fa-calendar-check w-5 h-5"></i>
        <span class="ml-3 text-sm font-semibold">Horários Fixos</span>
    </a>
</li>

                <hr>

                <!-- Atalho para visualizar o site -->
<li>
    <a href="{{ route('client.schedule.index') }}" target="_blank" 
        class="relative flex flex-row items-center h-12 focus:outline-none hover:bg-gray-800 text-gray-300 hover:text-yellow-400 border-l-4 border-transparent hover:border-yellow-400 px-6 transition">
        <i class="fas fa-eye w-5 h-5"></i>
        <span class="ml-3 text-sm font-semibold">Ver Site</span>
    </a>
</li>


<hr>

                <!-- Sair -->
                <li>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="relative flex flex-row items-center h-12 w-full focus:outline-none hover:bg-yellow-700 text-gray-300 hover:text-white border-l-4 border-transparent hover:border-yellow-500 px-6 transition">
                            <i class="fas fa-sign-out-alt w-5 h-5"></i>
                            <span class="ml-3 text-sm font-semibold">Sair</span>
                        </button>
                    </form>
                </li>
            </ul>
        </div>
    </div>
</div>
