<style>
    div.sp_container {
        display: flex;
        justify-content: center;
        align-items: center;
        position: absolute;
        top: 0;
        left: 0;
        bottom: 20px;
        right: 0;
    }
    .sp_container > div {
        width: 10px;
        height: 10px;
        border-radius: 100%;
        margin: 5px;
        background-image: linear-gradient(145deg, rgba(255,255,255,0.5) 0%, rgba(0,0,0,0) 100%);
        animation: bounce 1.5s 0.5s linear infinite;
    }
    .sp_container > .yellow {
        background-color: #feb60a;
    }

    .sp_container > .red {
        background-color: #ff0062;
        animation-delay: 0.1s;
    }

    .sp_container > .blue {
        background-color: #00dbf9;
        animation-delay: 0.2s;
    }

    .sp_container > .violet {
        background-color: #da00f7;
        animation-delay: 0.3s;
    }

    @keyframes bounce {
        0%, 50%, 100% {
            transform: scale(1);
            filter: blur(0px);
        }
        25% {
            transform: scale(0.6);
            filter: blur(3px);
        }
        75% {
            filter: blur(3px);
            transform: scale(1.4);
        }
    }
</style>
<div class="row kt-margin-b-15">
    <div class="col-lg-3">
        <div class="kt-portlet" style="background-color:#0014ff1a; height: 115.08px">
            <div class="sp_container">
                <div class="yellow"></div>
                <div class="red"></div>
                <div class="blue"></div>
                <div class="violet"></div>
            </div>
        </div>
    </div>
    <div class="col-lg-3">
        <div class="kt-portlet" style="background-color:#ff00c81a; height: 115.08px">
            <div class="sp_container">
                <div class="yellow"></div>
                <div class="red"></div>
                <div class="blue"></div>
                <div class="violet"></div>
            </div>
        </div>
    </div>
    <div class="col-lg-3">
        <div class="kt-portlet" style="background-color:#00ff431a; height: 115.08px">
            <div class="sp_container">
                <div class="yellow"></div>
                <div class="red"></div>
                <div class="blue"></div>
                <div class="violet"></div>
            </div>
        </div>
    </div>
    <div class="col-lg-3">
        <div class="kt-portlet" style="background-color:#00d0ff1a; height: 115.08px">
            <div class="sp_container">
                <div class="yellow"></div>
                <div class="red"></div>
                <div class="blue"></div>
                <div class="violet"></div>
            </div>
        </div>
    </div>
</div>
