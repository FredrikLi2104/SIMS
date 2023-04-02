<template>
    <canvas id="canvas" width="550" height="550"></canvas>

    <div id="tooltip" class="position-fixed shadow-sm bg-body rounded overflow-hidden width-250 d-none"
         style="z-index: 1;">
        <div class="row">
            <div class="col-2" :style="`background-color: ${taskColor};`"></div>
            <div class="col-10 p-1">
                <p class="fw-bold text-black mb-0">{{ taskTitle }}</p>
                <small class="fw-bold text-primary">{{ `${taskStart} - ${taskEnd}` }}</small>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: "TasksWheel",
    props: ['months', 'selectedYear', 'tasks'],
    emits: ['editTask'],
    data() {
        return {
            outerRingWidth: 40,
            taskSegments: [],
            taskTitle: null,
            taskColor: null,
            taskStart: null,
            taskEnd: null,
        }
    },
    methods: {
        degreeToRadian(deg) {
            return deg / 180 * Math.PI;
        },
        updateWheel(offsetForToday) {
            let self = this;
            let canvas = document.getElementById('canvas');
            let context = canvas.getContext('2d');
            let canvasWidth = canvas.width;
            let canvasHeight = canvas.height;
            context.resetTransform();
            context.beginPath();
            self.taskSegments = [];

            self.tasks.forEach((group, groupIndex) => {
                group.forEach((task) => {
                    let start = moment(task.start);
                    let end = moment(task.end);

                    let offset = -1 * offsetForToday;
                    for (let i = 0; i < start.month(); i++) {
                        offset += 30;
                    }
                    offset += 30 / start.daysInMonth() * start.date();

                    let endAngle = 0;
                    for (let i = start.month() + 1; i < end.month(); i++) {
                        endAngle += 30;
                    }

                    if (!start.isSame(end, 'month')) {
                        endAngle += 30 / end.daysInMonth() * end.date();
                        endAngle += 30 / start.daysInMonth() * (start.daysInMonth() - start.date());
                    } else {
                        endAngle += 30 / end.daysInMonth() * (end.date() - start.date());
                    }

                    let ringWidth = 20;
                    let gap = 4;
                    let radius = (canvasWidth / 2 - this.outerRingWidth - ringWidth / 2 - gap) - groupIndex * (ringWidth + gap);
                    let startAngle = self.degreeToRadian(-90 + offset + .2);
                    endAngle = self.degreeToRadian(-90 + endAngle + offset + .8);
                    context.beginPath();
                    let path = new Path2D();
                    path.arc(canvasWidth / 2, canvasHeight / 2, radius, startAngle, endAngle);
                    self.taskSegments.push({task: task, path: path});
                    context.lineWidth = 20;
                    context.strokeStyle = task.color;
                    context.stroke(path);
                });
            });
        },
        drawWheel() {
            let self = this;
            let canvas = document.getElementById('canvas');
            let context = canvas.getContext('2d');
            let canvasWidth = canvas.width;
            let canvasHeight = canvas.height;
            let lineWidth = this.outerRingWidth;
            context.resetTransform();
            context.clearRect(0, 0, canvasWidth, canvasHeight);
            context.beginPath();
            context.arc(canvasWidth / 2, canvasHeight / 2, canvasWidth / 2, 0, 2 * Math.PI);
            context.fillStyle = '#ededed';
            context.fill();

            let angle = this.degreeToRadian(360);
            let today = moment();
            let offset = -1;
            for (let i = 0; i < today.month(); i++) {
                offset += 30;
            }
            offset += 30 / today.daysInMonth() * today.date();

            for (let i = 0; i < 12; i++) {
                let startAngle = i * angle / 12 - this.degreeToRadian(offset + 90);
                context.beginPath();
                context.arc(canvasWidth / 2, canvasHeight / 2, canvasWidth / 2 - lineWidth / 2, startAngle, startAngle + angle / 12);
                context.lineWidth = lineWidth;
                context.strokeStyle = i % 2 === 0 ? '#ededed' : '#ffffff';
                context.stroke();
            }

            context.font = '24px "Montserrat", Helvetica, Arial, serif';
            context.fillStyle = '#636363';
            context.textAlign = 'center';
            context.beginPath();
            context.translate(canvasWidth / 2, canvasHeight / 2);
            context.rotate(0);

            for (let i = 0; i < 12; i++) {
                let startAngle = angle / 12;
                let center = startAngle / 2;
                context.save();
                context.rotate(i * startAngle - this.degreeToRadian(offset) + center);
                context.textBaseline = 'middle';
                context.fillText(this.months[i], 0, lineWidth / 2 - canvasWidth / 2);
                context.restore();
            }

            // today marker
            context.resetTransform();
            context.beginPath();
            context.moveTo(canvasWidth / 2, 9);
            context.arc(canvasWidth / 2, 9, 12, self.degreeToRadian(-135), self.degreeToRadian(-45));
            context.fillStyle = '#7367f0';
            context.fill();

            this.updateWheel(offset + 1);
        },
        handleTaskHover() {
            let self = this;
            let canvas = document.getElementById('canvas');
            let context = canvas.getContext('2d');
            canvas.addEventListener('mousemove', function (evt) {
                let segment = self.taskSegments.find(segment => context.isPointInStroke(segment.path, evt.offsetX, evt.offsetY));

                if (segment !== undefined) {
                    canvas.style.cursor = 'pointer';
                    self.taskTitle = segment.task.title;
                    self.taskColor = segment.task.color;
                    self.taskStart = segment.task.start;
                    self.taskEnd = segment.task.end;
                    document.getElementById('tooltip').style.top = (evt.clientY + 14) + 'px';
                    document.getElementById('tooltip').style.left = (evt.clientX + 14) + 'px';
                    document.getElementById('tooltip').classList.remove('d-none');
                } else {
                    canvas.style.cursor = 'auto';
                    document.getElementById('tooltip').classList.add('d-none');
                }
            });
        },
        handleTaskClick() {
            let self = this;
            let canvas = document.getElementById('canvas');
            let context = canvas.getContext('2d');
            canvas.addEventListener('click', function (evt) {
                let segment = self.taskSegments.find(segment => context.isPointInStroke(segment.path, evt.offsetX, evt.offsetY));

                if (segment !== undefined) {
                    self.$emit('editTask', segment.task.id);
                    document.getElementById('tooltip').classList.add('d-none');
                }
            });
        }
    },
    mounted() {
        this.drawWheel();
        this.handleTaskHover();
        this.handleTaskClick();
    },
    watch: {
        tasks() {
            this.drawWheel();
        }
    }
}
</script>

<style scoped>

</style>
