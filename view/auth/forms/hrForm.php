<div id="hr-form" class="hidden">
    <div
        class="flex items-center gap-2 bg-[#ecf3fa] border border-[#d1ddeb] rounded-full px-4 py-2 mb-6 text-xs text-[#1e3a5f]">
        <span>HR Manager portal</span>
    </div>

    <form action="#" method="post" class="space-y-5">
        <div>
            <label class="block text-sm font-medium text-[#2c3f4f] mb-1.5">
                <i class="fa fa-id-badge mr-1.5 text-[#5b7a95] text-xs"></i>HR email
            </label>
            <input type="text" name="hr_email" placeholder="Enter your email" value=""
                class="w-full px-4 py-2.5 bg-white border border-[#cad3df] rounded-lg text-[#1a2b36] text-sm placeholder-[#8f9fb0] focus:outline-none focus:border-[#1e3a5f] focus:ring-2 focus:ring-[#1e3a5f]/10 transition-all">
        </div>

        <div>
            <label class="block text-sm font-medium text-[#2c3f4f] mb-1.5">
                <i class="fa fa-lock mr-1.5 text-[#5b7a95] text-xs"></i>Password
            </label>
            <input type="password" name="hr_password" placeholder="" value=""
                class="w-full px-4 py-2.5 bg-white border border-[#cad3df] rounded-lg text-[#1a2b36] text-sm placeholder-[#8f9fb0] focus:outline-none focus:border-[#1e3a5f] focus:ring-2 focus:ring-[#1e3a5f]/10 transition-all">
        </div>
        <button type="submit"
            class="w-full bg-[#1e3a5f] hover:bg-primary-hover text-white font-medium py-2.5 px-4 rounded-lg border border-[#122b42] transition-colors flex items-center justify-center gap-2 text-sm shadow-sm">
            <i class="fa fa-arrow-right-to-bracket"></i>
            Sign in
        </button>
    </form>
</div>