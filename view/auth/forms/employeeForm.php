<div id="employee-form" class="block">
    <div
        class="flex items-center gap-2 bg-[#ecf3fa] border border-[#d1ddeb] rounded-full px-4 py-2 mb-6 text-xs text-[#1e3a5f]">
        <span>Employee portal · ESS access</span>
    </div>

    <form action="#" method="post" class="space-y-5">
        <div>
            <label class="block text-sm font-medium text-[#2c3f4f] mb-1.5">
                <i class="fa fa-id-card mr-1.5 text-[#5b7a95] text-xs"></i>Employee ID
            </label>
            <input type="text" name="username" placeholder="Enter employee id" value=""
                class="w-full px-4 py-2.5 bg-white border border-[#cad3df] rounded-lg text-[#1a2b36] text-sm placeholder-[#8f9fb0] focus:outline-none focus:border-[#1e3a5f] focus:ring-2 focus:ring-[#1e3a5f]/10 transition-all">
        </div>

        <div>
            <label class="block text-sm font-medium text-[#2c3f4f] mb-1.5">
                <i class="fa fa-lock mr-1.5 text-[#5b7a95] text-xs"></i>Password
            </label>
            <input type="password" name="password" placeholder="" value=""
                class="w-full px-4 py-2.5 bg-white border border-[#cad3df] rounded-lg text-[#1a2b36] text-sm placeholder-[#8f9fb0] focus:outline-none focus:border-[#1e3a5f] focus:ring-2 focus:ring-[#1e3a5f]/10 transition-all">
        </div>
        <button type="submit"
            class="w-full bg-[#1e3a5f] hover:bg-primary-hover text-white font-medium py-2.5 px-4 rounded-lg border border-[#122b42] transition-colors flex items-center justify-center gap-2 text-sm shadow-sm">
            <i class="fa fa-arrow-right-to-bracket"></i>
            Sign in
        </button>
    </form>
</div>