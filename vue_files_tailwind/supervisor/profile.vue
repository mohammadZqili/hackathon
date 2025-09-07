<template>
    <div class="w-full relative bg-mintcream-100 h-[1244px] overflow-hidden text-left text-sm text-gray font-ruman-one">
        <div class="absolute top-[0px] left-[0px] w-[1440px] flex flex-row items-start justify-start py-6 px-4 box-border gap-2">
            <Sidebar />
            <div class="flex-1 h-[778px] flex flex-col items-start justify-start gap-4 text-base text-seagreen">
                <Header />
                <div class="w-[960px] overflow-hidden flex flex-col items-start justify-start py-5 px-0 box-border max-w-[960px] text-gray font-space-grotesk">
                    <div class="self-stretch flex flex-row items-start justify-between flex-wrap content-start p-4 text-[32px]">
                        <div class="w-72 flex flex-col items-start justify-start min-w-[288px]">
                            <b class="self-stretch relative leading-10">My Profile</b>
                        </div>
                    </div>
                    <div class="self-stretch flex flex-row items-start justify-start p-4 text-center text-[22px]">
                        <div class="flex-1 flex flex-col items-center justify-start">
                            <div class="flex-1 flex flex-col items-center justify-start gap-4">
                                <img class="w-32 relative rounded-[64px] h-32 overflow-hidden shrink-0 object-cover min-h-[128px]" alt="" src="Depth 7, Frame 0.png" />
                                <div class="flex flex-col items-center justify-center">
                                    <div class="h-7 flex flex-col items-center justify-start">
                                        <b class="self-stretch relative leading-7">Sophia Clark</b>
                                    </div>
                                    <div class="flex flex-col items-center justify-start text-base text-seagreen">
                                        <div class="self-stretch relative leading-6">sophia.clark@email.com</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="flex flex-row items-start justify-start">
                        <div class="flex flex-row items-end justify-start flex-wrap content-end py-3 px-4 box-border max-w-[480px]">
                            <div class="flex-1 flex flex-col items-start justify-start min-w-[160px]">
                                <div class="self-stretch flex flex-col items-start justify-start pt-0 px-0 pb-2">
                                    <div class="self-stretch relative leading-6 font-medium">Name</div>
                                </div>
                                <div class="self-stretch rounded-xl bg-mintcream-200 h-8 overflow-hidden shrink-0" />
                            </div>
                        </div>
                        <div class="flex flex-row items-end justify-start flex-wrap content-end py-3 px-4 box-border max-w-[480px]">
                            <div class="flex-1 flex flex-col items-start justify-start min-w-[160px]">
                                <div class="self-stretch flex flex-col items-start justify-start pt-0 px-0 pb-2">
                                    <div class="self-stretch relative leading-6 font-medium">Email</div>
                                </div>
                                <div class="self-stretch rounded-xl bg-mintcream-200 h-8 overflow-hidden shrink-0" />
                            </div>
                        </div>
                    </div>
                    <div class="flex flex-row items-start justify-start">
                        <div class="flex flex-row items-end justify-start flex-wrap content-end py-3 px-4 box-border max-w-[480px]">
                            <div class="flex-1 flex flex-col items-start justify-start min-w-[160px]">
                                <div class="self-stretch flex flex-col items-start justify-start pt-0 px-0 pb-2">
                                    <div class="self-stretch relative leading-6 font-medium">Date of Birth</div>
                                </div>
                                <div class="self-stretch rounded-xl bg-mintcream-200 h-8 overflow-hidden shrink-0" />
                            </div>
                        </div>
                        <div class="flex flex-row items-end justify-start flex-wrap content-end py-3 px-4 box-border max-w-[480px]">
                            <div class="flex-1 flex flex-col items-start justify-start min-w-[160px]">
                                <div class="self-stretch flex flex-col items-start justify-start pt-0 px-0 pb-2">
                                    <div class="self-stretch relative leading-6 font-medium">Phone Number</div>
                                </div>
                                <div class="self-stretch rounded-xl bg-mintcream-200 h-8 overflow-hidden shrink-0" />
                            </div>
                        </div>
                    </div>
                    <div class="flex flex-row items-end justify-start flex-wrap content-end py-3 px-4 box-border max-w-[480px]">
                        <div class="flex-1 flex flex-col items-start justify-start min-w-[160px]">
                            <div class="self-stretch flex flex-col items-start justify-start pt-0 px-0 pb-2">
                                <div class="self-stretch relative leading-6 font-medium">National ID</div>
                            </div>
                            <div class="self-stretch rounded-xl bg-mintcream-200 h-8 overflow-hidden shrink-0" />
                        </div>
                    </div>
                    <div class="flex flex-row items-start justify-start">
                        <div class="flex flex-row items-end justify-start flex-wrap content-end py-3 px-4 box-border max-w-[480px]">
                            <div class="flex-1 flex flex-col items-start justify-start min-w-[160px]">
                                <div class="self-stretch flex flex-col items-start justify-start pt-0 px-0 pb-2">
                                    <div class="self-stretch relative leading-6 font-medium">Password</div>
                                </div>
                                <div class="self-stretch rounded-xl bg-mintcream-200 h-8 overflow-hidden shrink-0" />
                            </div>
                        </div>
                        <div class="flex flex-row items-end justify-start flex-wrap content-end py-3 px-4 box-border max-w-[480px]">
                            <div class="flex-1 flex flex-col items-start justify-start min-w-[160px]">
                                <div class="self-stretch flex flex-col items-start justify-start pt-0 px-0 pb-2">
                                    <div class="self-stretch relative leading-6 font-medium">Confirm Password</div>
                                </div>
                                <div class="self-stretch rounded-xl bg-mintcream-200 h-8 overflow-hidden shrink-0" />
                            </div>
                        </div>
                    </div>
                    <div class="flex flex-row items-start justify-start">
                        <div class="flex flex-row items-end justify-start flex-wrap content-end py-3 px-4 box-border gap-x-4 gap-y-0 max-w-[480px]">
                            <div class="w-[448px] flex flex-col items-start justify-start min-w-[160px]">
                                <div class="self-stretch flex flex-col items-start justify-start pt-0 px-0 pb-2">
                                    <div class="self-stretch relative leading-6 font-medium">Occupation</div>
                                </div>
                            </div>
                            <div class="w-[464px] flex flex-row items-start justify-start p-[11px] box-border gap-2 text-[9.78px]">
                                <div class="flex-1 rounded-[8.38px] border-honeydew border-solid border-[0.7px] box-border h-[37px] flex flex-row items-center justify-start p-[10.5px] gap-[11.2px]">
                                    <img class="w-3.5 relative rounded-[6.98px] h-3.5 overflow-hidden shrink-0" alt="" src="Depth 6, Frame 0.svg" />
                                    <div class="flex flex-col items-start justify-start">
                                        <div class="w-[564.2px] flex-1 flex flex-col items-start justify-start">
                                            <div class="self-stretch relative leading-[14.66px] font-medium">Student</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="flex-1 rounded-[8.38px] border-honeydew border-solid border-[0.7px] box-border h-[37px] flex flex-row items-center justify-start p-[10.5px] gap-[11.2px]">
                                    <div class="w-3.5 rounded-[6.98px] border-honeydew border-solid border-[1.4px] box-border h-3.5" />
                                    <div class="flex flex-col items-start justify-start">
                                        <div class="w-[564.2px] flex-1 flex flex-col items-start justify-start">
                                            <div class="self-stretch relative leading-[14.66px] font-medium">Employee</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="flex flex-row items-end justify-start flex-wrap content-end py-3 px-4 box-border max-w-[480px]">
                            <div class="flex-1 flex flex-col items-start justify-start min-w-[160px]">
                                <div class="self-stretch flex flex-col items-start justify-start pt-0 px-0 pb-2">
                                    <div class="self-stretch relative leading-6 font-medium">Job Title</div>
                                </div>
                                <div class="self-stretch rounded-xl bg-mintcream-200 h-8 overflow-hidden shrink-0" />
                            </div>
                        </div>
                    </div>
                    <div class="self-stretch flex flex-row items-start justify-end py-3 px-4 text-center text-sm">
                        <div class="rounded-xl bg-mediumseagreen h-10 overflow-hidden flex flex-row items-center justify-center py-0 px-4 box-border min-w-[84px] max-w-[480px]">
                            <div class="overflow-hidden flex flex-col items-center justify-start">
                                <b class="self-stretch relative leading-[21px] overflow-hidden text-ellipsis whitespace-nowrap">Save Changes</b>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import Sidebar from '../../vue_files/common/Sidebar.vue';
import Header from '../../vue_files/common/Header.vue';
</script>

<style scoped>
/* Add any specific styles for the profile page here */
</style>