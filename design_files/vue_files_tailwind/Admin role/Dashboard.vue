<template>
    <div class="w-full relative bg-mintcream-100 h-[1024px] overflow-hidden text-left text-sm text-gray font-ruman-one">
        <div class="absolute top-[0px] left-[0px] w-[1440px] h-[1024px] flex flex-row items-start justify-start py-6 px-4 box-border gap-2">
            <SupervisorSidebar />
            <div class="flex-1 flex flex-col items-start justify-start gap-4 text-base text-seagreen">
                <SupervisorHeader />
                <div class="self-stretch flex flex-col items-start justify-start text-gray font-space-grotesk">
                    <div class="w-[908px] h-[880px] overflow-hidden shrink-0 flex flex-col items-start justify-start max-w-[960px]">
                        <div class="self-stretch flex flex-row items-start justify-between flex-wrap content-start p-4 text-[32px]">
                            <div class="w-72 flex flex-col items-start justify-start min-w-[288px]">
                                <b class="self-stretch relative leading-10">Dashboard</b>
                            </div>
                        </div>
                        <div class="self-stretch flex flex-col items-start justify-start pt-5 px-4 pb-3 text-[22px]">
                            <b class="self-stretch relative leading-7">Summary</b>
                        </div>
                        <div class="self-stretch flex flex-col items-start justify-start p-4">
                            <div class="self-stretch rounded-xl flex flex-row items-start justify-between gap-0">
                                <div class="w-[573px] h-[161px] flex flex-col items-start justify-start gap-4">
                                    <div class="self-stretch flex flex-col items-start justify-start gap-1">
                                        <div class="self-stretch h-5 flex flex-col items-start justify-start">
                                            <b class="self-stretch relative leading-5">Ideas</b>
                                        </div>
                                        <div class="self-stretch flex flex-col items-start justify-start text-sm text-seagreen">
                                            <div class="self-stretch relative leading-[21px]">Review ideas with your track.</div>
                                        </div>
                                    </div>
                                    <div class="rounded-xl bg-mintcream-200 h-8 overflow-hidden shrink-0 flex flex-row items-center justify-center py-0 px-4 box-border min-w-[84px] max-w-[480px] text-center text-sm">
                                        <div class="overflow-hidden flex flex-col items-center justify-start">
                                            <div class="self-stretch relative leading-[21px] font-medium overflow-hidden text-ellipsis whitespace-nowrap">Review Ideas</div>
                                        </div>
                                    </div>
                                </div>
                                <img class="flex-1 relative rounded-xl max-w-full overflow-hidden h-[161px] object-cover" alt="" src="Depth 6, Frame 1.png" />
                            </div>
                        </div>
                        <div class="self-stretch flex flex-col items-start justify-start p-4">
                            <div class="self-stretch rounded-xl flex flex-row items-start justify-between gap-0">
                                <div class="w-[573px] h-[161px] flex flex-col items-start justify-start gap-4">
                                    <div class="self-stretch flex flex-col items-start justify-start gap-1">
                                        <div class="self-stretch h-5 flex flex-col items-start justify-start">
                                            <b class="self-stretch relative leading-5">Upcoming Workshops</b>
                                        </div>
                                        <div class="self-stretch flex flex-col items-start justify-start text-sm text-seagreen">
                                            <div class="self-stretch relative leading-[21px]">See the schedule for upcoming workshops and training sessions.</div>
                                        </div>
                                    </div>
                                    <div class="rounded-xl bg-mintcream-200 h-8 overflow-hidden shrink-0 flex flex-row items-center justify-center py-0 px-4 box-border min-w-[84px] max-w-[480px] text-center text-sm">
                                        <div class="overflow-hidden flex flex-col items-center justify-start">
                                            <div class="self-stretch relative leading-[21px] font-medium overflow-hidden text-ellipsis whitespace-nowrap">View Workshops</div>
                                        </div>
                                    </div>
                                </div>
                                <img class="flex-1 relative rounded-xl max-w-full overflow-hidden h-[161px] object-cover" alt="" src="Depth 6, Frame 1.png" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import SupervisorSidebar from '../../vue_files/supervisor/SupervisorSidebar.vue';
import SupervisorHeader from '../../vue_files/supervisor/SupervisorHeader.vue';
</script>

<style scoped>
/* Add any specific styles for the dashboard here */
</style>