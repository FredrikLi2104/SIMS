<template>
    <canvas id="canvas" width="550" height="550"></canvas>
</template>

<script>
export default {
    name: "TasksWheel",
    props: ['months', 'selectedYear', 'tasks'],
    data() {
        return {
            outerRingWidth: 40,
        }
    },
    methods: {
        degreeToRadian(deg) {
            return deg / 180 * Math.PI;
        },
        updateWheel() {
            let self = this;
            let yearStart = moment().year(this.selectedYear).startOf('year');
            let yearEnd = moment().year(this.selectedYear).endOf('year');
            let canvas = document.getElementById('canvas');
            let context = canvas.getContext('2d');
            let canvasWidth = canvas.width;
            let canvasHeight = canvas.height;
            context.resetTransform();
            context.beginPath();

            self.tasks.forEach((group, groupIndex) => {
                group.forEach((task) => {
                    let start = moment(task.start);
                    let end = moment(task.end);

                    let offset = -1;
                    for (let i = 0; i < start.month(); i++) {
                        offset += 30;
                    }
                    offset += 30 / start.daysInMonth() * start.date();

                    let endAngle = 0;
                    for (let i = start.month() + 1; i < end.month(); i++) {
                        endAngle += 30;
                    }
                    endAngle += 30 / end.daysInMonth() * end.date();

                    if (!start.isSame(end, 'month')) {
                        endAngle += 30 / start.daysInMonth() * (start.daysInMonth() - start.date());
                    }

                    let ringWidth = 20;
                    let gap = 4;
                    let radius = (canvasWidth / 2 - this.outerRingWidth - ringWidth / 2 - gap) - groupIndex * (ringWidth + gap);
                    let startAngle = self.degreeToRadian(-90 + offset);
                    endAngle = self.degreeToRadian(-90 + endAngle + offset);
                    context.beginPath();
                    context.arc(canvasWidth / 2, canvasHeight / 2, radius, startAngle, endAngle);
                    context.lineWidth = 20;
                    context.strokeStyle = task.color;
                    context.stroke();
                });
            });
        },
        drawWheel() {
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
            for (let i = 0; i < 12; i++) {
                let startAngle = i * angle / 12;
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
                context.rotate(i * startAngle + center);
                context.textBaseline = 'middle';
                context.fillText(this.months[i], 0, lineWidth / 2 - canvasWidth / 2);
                context.restore();
            }

            this.updateWheel();
        }
    },
    mounted() {
        this.drawWheel();
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
