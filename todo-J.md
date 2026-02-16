Fonctionnalites :  Dispatch automatique par ordre chronologique [J]

Ce que j ai besoin pour cette fonctionnalite:
    A = les liste des articles dans le stock  des dons avec leur qtt (la qtt > 0) 
    pour tout les element de A 
        {
            B = trouver les listes des besoins par ville 
            trier par date et si il y a deux besoin de meme date alors on priorise la colonne create_at (date de saisie du besoin);
        }
Base : bn_distribution
    route : GET /autoDispatch();


Backend  :
        route : GET /autoDistribution
        contoller  : distributionController = > autoDistribution
Frontend  :
    un bouton seulement  qui appelle route 