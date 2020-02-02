<template>
    <div class="container">
        <div class="row justify-content-center">
            <div class="form-group  mb-8 justify-content-center">
                <div>
                    <button type="submit" class="btn btn-primary" @click="home">
                        New Game
                    </button>
                    <button type="submit" class="btn btn-primary" @click="save">
                       Save Game
                    </button>

                </div>
            </div>
        </div>
        <div class="row justify-content-center">

            <div>
                <div class="card justify-content-center">
                    <div class="card-header"><span id="minutes"></span>:<span id="seconds"></span></div>
                    <div class="card-body">
                        <div class="row" v-for="(row, index) in this.grid">
                            <div v-for="(column, id) in row" @click="update(index, id)">
                                <cell :item="column"></cell>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>



</template>

<script>
    import Cell from './Cell.vue';
    import EventBus from './eventBus.js';


    export default {
        // props: ['rows','columns','mines'],
        data() {
            return {
                 rows: null,
                columns:null,
                 mines: null,
                csrf: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                listMines: [],
                id: this.$route.params.id,
                tracking: null,
                grid : [],
                remainingCells : 0,
                data: {},

            }
        },

        components: {
            Cell
        },

        mounted() {
            this.refresh();
        },


        created() {
            this.TimeTracker();

            let self = this;

            let uri = `/api/play-game/${this.$route.params.id}`;
            this.axios.get(uri).then((response) => {

                this.rows = response.data.num_rows;
                this.columns = response.data.num_columns;
                this.mines = response.data.num_mines;


                self.grid = [];

                self.createGrid().setRandomMines().setAdjacentCells();



            });

            EventBus.$on('mineFound', () => {
                self.flipAll();

                EventBus.$emit('gameLost');
            });
            EventBus.$on('flippedCell', (cell) => {
                this.remainingCells--;

                if (cell.isEmpty) {
                    self.flipNeighbours(cell);
                }
            });




        },

        watch: {
            remainingCells(newValue) {
                if (newValue === 0) {
                    console.log('emmiting won');
                    EventBus.$emit('gameWon');
                }
            }

        },

        methods: {


            createGrid() {


                for (let x = 0; x < this.rows; x++) {
                    let row = [];

                    for (let y = 0; y < this.columns; y++) {
                        let cell = this.createCell(x, y);

                        row.push(cell);
                    }

                    this.grid.push(row);
                }

                this.remainingCells = this.rows * this.columns - this.mines;

                return this;
            },

            createCell(row, column) {

                return {
                    row,
                    column,
                    isMine : false,
                    isEmpty : false,
                    isLocked : false,

                    adjacentCells : [],
                    adjacentMines:  0,
                    status : 'facedown',
                };
            },

            setRandomMines() {
                let remainingMines = this.mines;


                        while (remainingMines > 0) {
                            let randomRow = Math.floor(Math.random() * this.rows);
                            let randomColumn = Math.floor(Math.random() * this.columns);
                          //  alert(randomColumn);

                            if (this.isCellOnGrid(randomRow, randomColumn)) {
                                let candidateCell = this.grid[randomRow][randomColumn];


                                if (! candidateCell.isMine) {
                                    candidateCell.isMine = true;
                                    this.SetMine(randomRow, randomColumn,candidateCell.isMine);

                                    remainingMines--;
                                    console.log('setmine', candidateCell);
                                }
                            }
                        }
                        return this;



            },

            isCellOnGrid(x, y) {

                return x >= 0 && x < this.rows &&
                    y >= 0 && y < this.columns;
            },

            setAdjacentCells(cell) {
                let borderCoords =  [
                    {row : -1, column : -1},
                    {row : -1, column : 0},
                    {row : -1, column : 1},
                    {row : 0, column : -1},
                    {row : 0, column : 1},
                    {row : 1, column : -1},
                    {row : 1, column : 0},
                    {row : 1, column : 1}
                ]

                this.grid.forEach((row) => {
                    row.forEach((cell) => {
                        borderCoords.forEach((coord) => {
                            let neighborRow = coord.row + cell.row;

                            let neighborColumn = coord.column + cell.column;

                            if (this.isCellOnGrid(neighborRow, neighborColumn))  {


                                let neighborCell = this.grid[neighborRow][neighborColumn];

                                cell.adjacentCells.push(neighborCell);

                                if (neighborCell.isMine) {
                                    cell.adjacentMines++;
                                }
                            }
                        })

                        cell.isEmpty = cell.isMine === false && cell.adjacentMines === 0;

                    });
                });

                return this;
            },

            flipAll() {
                  this.grid.forEach((row) => {
                    row.forEach((cell) => {

                        cell.status = "open";
                    });
                });
            },

            flipNeighbours(cell) {
                cell.isLocked = true;

                cell.adjacentCells.forEach((neighbour) => {
                    if (neighbour.status !== "open") {
                        neighbour.status = "open";

                        this.remainingCells--;
                    }
                });

                cell.adjacentCells.forEach((neighbour) => {
                    if (! neighbour.isLocked && neighbour.isEmpty) {
                        this.flipNeighbours(neighbour);
                    }
                });
            },


            SetMine(x,y,m){
                let uri = `/api/set-mine/${this.$route.params.id}`;
                this.axios.post(uri,{x: x, y:y, m:m}).then((response) => {


                })
                    .catch(error => {
                        if (error.response.status === 422) {
                            this.errors = error.response.data
                        }
                    });
            },

            SetAdjacent(x,y,m){
                let uri = `/api/set-adjacent/${this.$route.params.id}`;
                this.axios.post(uri,{x: x, y:y, m:m}).then((response) => {


                })
                    .catch(error => {
                        if (error.response.status === 422) {
                            this.errors = error.response.data
                        }
                    });
            },
            TimeTracker(){
                var sec = 0;
                function pad ( val ) { return val > 9 ? val : "0" + val; }
                setInterval( function(){
                    document.getElementById("seconds").innerHTML=pad(++sec%60);
                    document.getElementById("minutes").innerHTML=pad(parseInt(sec/60,10));
                }, 1000);

            },
            update: function (valor,id){
                this.mute = true;
                let uri = `/api/revealed/${this.$route.params.id}`;
                window.axios.post(uri, {x: valor, y:id}).then(() => {

                    this.mute = false;
                });

            },
            home(){
                this.$router.push({name: 'home'});
                location.reload();
            },



        }
    }

</script>


<style>
    .grid {
        text-align: center;
        margin-top: 100px;
    }
    .row {
        display: flex;
    }
</style>
