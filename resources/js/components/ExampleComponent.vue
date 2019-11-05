<template>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="row  mt-5">
                    <div class="col-md-5">
                        <div class="card">
                            <div class="card-header">
                                Calendar
                            </div>
                            <div class="card-body">
                                <form role="form" @submit.prevent="submit">
                                    <div class="form-group">

                                        <label for="exampleInputEmail1">
                                            Event
                                        </label>
                                        <input type="text" class="form-control" id="exampleInputEmail1" v-model="fields.event" />
                                        <div v-if="errors && errors.event" class="text-danger">{{ errors.event[0] }}</div>
                                    </div>
                                    <div class="form-group">
                                        <label for="exampleInputPassword1">
                                            From
                                        </label>
                                        <datepicker :input-class="'form-control'" style="width:auto" v-model="fields.from"  :format="customFormatter" :placeholder="'yyyy-mm-dd'"></datepicker>
                                        <div v-if="errors && errors.from" class="text-danger">{{ errors.from[0] }}</div>
                                        <label for="exampleInputPassword1">
                                            To
                                        </label>
                                        <datepicker :input-class="'form-control'" style="width:auto" v-model="fields.to"  :format="customFormatter" :placeholder="'yyyy-mm-dd'"></datepicker>
                                        <div v-if="errors && errors.to" class="text-danger">{{ errors.to[0] }}</div>
                                    </div>
                                    <div class="form-group d-inline">
                                        <div v-for="day in dates" class="d-inline">
                                            <input type="checkbox" class="form-check-inline" :value="day" style="margin-right:.5rem" v-model="fields.days"> {{ day}}
                                        </div>
                                        <div v-if="errors && errors.days" class="text-danger">{{ errors.days[0] }}</div>
                                    </div>
                                    <button type="submit" class="btn btn-primary mt-3" @click="">
                                        Submit
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-7">
                        <table v-for="(item, key) in calendarDays" class="table table-bordered">
                            <thead style="background-color:#333">
                                <tr>
                                    <td>{{ key }}</td>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="day in item">
                                    <td :class="day.selected ? 'table-success' : null">{{ day.date }} <span v-if="day.selected" class="ml-2">{{ eventName}} </span></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import Datepicker from 'vuejs-datepicker';
    import moment from 'moment'

    export default {
        data() {
            return {
                fields: {
                    days: [],
                },
                errors: {},
                dates: [
                  'Mon',
                  'Tue',
                  'Wed',
                  'Thu',
                  'Fri',
                  'Sat',
                  'Sun'
                ],
                moment: moment,
                calendarDays: {},
                months: [],
                eventName: null,
            }
        },
        created() {
            this.display(moment().startOf('month').format('YYYY-MM-DD'), moment().endOf('month').format('YYYY-MM-DD'), ['Mon']);

        },
        methods: {
            submit() {
                this.errors = {};
                axios.post('/submit', this.fields).then(response => {
                    this.calendarDays = {};
                    this.eventName = response.data.event;
                    this.display(moment(response.data.from), moment(response.data.to), response.data.days);
                }).catch(error => {
                    if (error.response.status === 422) {
                        this.errors = error.response.data.errors || {};
                    }
                });
            },
            customFormatter(date) {
                return moment(date).format('YYYY-MM-DD');
            },
            display(start, to, days) {
                let from = moment(start);
                let to_date = moment(to);
                let diff = to_date.diff(from, 'days');
                let month = from.format('MMMM YYYY');
                if (this.calendarDays.hasOwnProperty(month) === false) {
                    this.calendarDays[month] = []
                }
                this.calendarDays[month].push({
                    'date': from.format('DD ddd'),
                    'selected' : days.includes(from.format('DD ddd').split(" ")[1])
                });
                for (let i = 0; i < diff; i++) {
                    let from_date = from.add(1, 'days');
                    let month = from_date.format('MMMM YYYY');
                    let selected = days.includes(from_date.format('DD ddd').split(" ")[1]);
                    if (this.calendarDays.hasOwnProperty(month) === false) {
                        this.calendarDays[month] = []
                    }
                    this.calendarDays[month].push({
                        'date': from_date.format('DD ddd'),
                        'selected' : selected

                    });
                }
            }
        },
        components: {
            Datepicker
        }
    }
</script>
