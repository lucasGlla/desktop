<nav id="sidebar">
        <div id="sidebar_content">
            <div id="User">
            <img src="src/img/logo.png" id="user_avatar" alt="Avatar">

            <p id="user_infos">
                <?php echo "<span class='item-description'> $logado</span>"; ?>
                <span class="item-description"><?php echo $nivel_acesso;?></span>
            </p>
            </div>
        
            <ul id="side-items">
                <li class="side-item">
                    <a href="index.php">
                        <i class="fa-solid fa-desktop"></i>
                        <span class="item-description">
                            resumo
                        </span>
                    </a>
                </li>

                <li class="side-item">
                    <a href="users.php">
                        <i class="fa-solid fa-user"></i>
                        <span class="item-description">
                            Usuarios
                        </span>
                    </a>
                </li>

                <li class="side-item">
                    <a href="chamados.php">
                        <i class="fa-solid fa-list"></i>
                        <span class="item-description">
                            Tarefas
                        </span>
                    </a>
                </li>

                <li class="side-item">
                    <a href="relatorio.php">
                        <i class="fa-solid fa-chart-pie"></i>
                        <span class="item-description">
                            Relatorio
                        </span>
                    </a>
                </li>

            </ul>

            <button id="open_btn">
                <i id="open_btn_icon" class="fa-solid fa-chevron-right"></i>
                </button>
        </div>

        <div id="logout">
            <button id="logout_btn">
                    <a href="./controller/logout.php">
                        <i class="fa-solid fa-right-from-bracket"></i>
                        <span class="item-description"> Sair </span>
                    </a>
            </button>
        </div>

</nav>