<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170720211942 extends AbstractMigration implements ContainerAwareInterface
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var array
     */
    protected $titles = [
        1	=> 'Business books',
        2	=> 'Foreign literature',
        3	=> 'Childrens books',
        4	=> 'Contemporary prose',
        5	=> 'Science fiction',
        6	=> 'Fantasy',
        7	=> 'Detectives',
        8	=> 'Adventure',
        9	=> 'Action books',
        10	=> 'Love story',
        11	=> 'Classics',
        12	=> 'Publicism',
        13	=> 'Religion',
        14	=> 'Humor',
        15	=> 'Poetry, dramaturgy',
        16	=> 'Psychology books',
        17	=> 'Home, family',
        18	=> 'Art',
        19	=> 'Science, education',
        20	=> 'Catalogs',
        21	=> 'Computers',
        22	=> 'Stories',
        23	=> 'Periodicals',
        24	=> 'Foreign business literature',
        25	=> 'Marketing, PR, advertising',
        26	=> 'Popular business',
        27	=> 'Job search, career',
        28	=> 'Management, staff recruitment',
        29	=> 'Personal finance',
        30	=> 'Corporate culture',
        31	=> 'Banking',
        32	=> 'Foreign economic activity',
        33	=> 'Office work',
        34	=> 'Accounting, taxation, audit',
        35	=> 'Small business',
        36	=> 'Real estate',
        37	=> 'Industry publications',
        38	=> 'Securities, investments',
        39	=> 'Economy',
        40	=> 'Foreign thrillers',
        41	=> 'Foreign childrens books',
        42	=> 'Modern foreign literature',
        43	=> 'Foreign science fiction',
        44	=> 'Foreign fantasy',
        45	=> 'Foreign detectives',
        46	=> 'Foreign adventures',
        47	=> 'Foreign romance novels',
        48	=> 'Foreign classics',
        49	=> 'Foreign ancient literature',
        50	=> 'Foreign publicism',
        51	=> 'Foreign esoteric and religious literature',
        52	=> 'Foreign humor',
        53	=> 'Foreign drama',
        54	=> 'Foreign poems',
        55	=> 'Foreign psychology',
        56	=> 'Foreign applied and popular science literature',
        57	=> 'Foreign educational literature',
        58	=> 'Foreign reference literature',
        59	=> 'Foreign computer literature',
        60	=> 'Foreign: other',
        61	=> 'Fairy tales',
        62	=> 'Childrens adventures',
        63	=> 'Childrens prose',
        64	=> 'Childrens detectives',
        65	=> 'Educational literature',
        66	=> 'Childrens fantasy',
        67	=> 'Childrens poems',
        68	=> 'Books for children: other',
        69	=> 'Amateur authors',
        70	=> 'Contemporary russian literature',
        71	=> 'Historical literature',
        72	=> 'Counterculture',
        73	=> 'Books about the war',
        74	=> 'Science fiction',
        75	=> 'Accidental travel',
        76	=> 'Humorous fiction',
        77	=> 'Historical fiction',
        78	=> 'Heroic fiction',
        79	=> 'Detective fiction',
        80	=> 'Space fiction',
        81	=> 'Social science fiction',
        82	=> 'Cyberpunk',
        83	=> 'Fighting fantasy',
        84	=> 'Books about vampires',
        85	=> 'Books about wizards',
        86	=> 'Horror and mysticism',
        87	=> 'Fantasy about dragons',
        88	=> 'Humorous fantasy',
        89	=> 'Historical fantasy',
        90	=> 'Urban fantasy',
        91	=> 'Love fantasy',
        92	=> 'Russian fantasy',
        93	=> 'Battle fantasy',
        94	=> 'Classical detectives',
        95	=> 'Ironic detectives',
        96	=> 'Historical detectives',
        97	=> 'Political detectives',
        98	=> 'Police detectives',
        99	=> 'Spy Detectives',
        100	=> 'Cool detective',
        101	=> 'Contemporary detectives',
        102	=> 'Historical adventures',
        103	=> 'Sea adventures',
        104	=> 'Travel books',
        105	=> 'Westerns',
        106	=> 'Adventure: other',
        107	=> 'Thrillers',
        108	=> 'Criminal action',
        109	=> 'Action: other',
        110	=> 'Short love stories',
        111	=> 'Action-romance novels',
        112	=> 'Historical romance novels',
        113	=> 'Erotic literature',
        114	=> 'Modern romance novels',
        115	=> 'Lovingly-fiction novels',
        116	=> 'Literature of the 18th century',
        117	=> 'Literature of the 19th century',
        118	=> 'Literature of the 20th century',
        119	=> 'Old russian literature',
        120	=> 'Russian classics',
        121	=> 'Soviet literature',
        122	=> 'Ancient oriental literature',
        123	=> 'European ancient literature',
        124	=> 'Myths. Legends. Epos',
        125	=> 'Ancient literature',
        126	=> 'Ancient: other',
        127	=> 'Classical prose',
        128	=> 'Biographies and memoirs',
        129	=> 'Aphorisms and quotations',
        130	=> 'Military affairs, special services',
        131	=> 'Publicism: other',
        132	=> 'Documentary literature',
        133	=> 'Esoterics',
        134	=> 'Religious texts',
        135	=> 'Religious studies',
        136	=> 'Religion: other',
        137	=> 'Anecdotes',
        138	=> 'Humorous prose',
        139	=> 'Humorous poems',
        140	=> 'Humor: other',
        141	=> 'Dramaturgy',
        142	=> 'Poetry',
        143	=> 'Personal growth',
        144	=> 'Sex and family psychology',
        145	=> 'Social psychology',
        146	=> 'General psychology',
        147	=> 'Classics of psychology',
        148	=> 'Child psychology',
        149	=> 'Psychotherapy and counseling',
        150	=> 'Parenting',
        151	=> 'Cooking',
        152	=> 'Health',
        153	=> 'Self improvement',
        154	=> 'Erotica, sex',
        155	=> 'Entertainment',
        156	=> 'Sports, fitness',
        157	=> 'Garden',
        158	=> 'Hobby, crafts',
        159	=> 'Nature and animals',
        160	=> 'Cars and traffic regulations',
        161	=> 'Home and Family: other',
        162	=> 'Pets',
        163	=> 'Do it yourself',
        164	=> 'Architecture',
        165	=> 'Fine arts, photography',
        166	=> 'Cinematography, theater',
        167	=> 'Criticism',
        168	=> 'Music, ballet',
        169	=> 'Biology',
        170	=> 'Medicine',
        171	=> 'Pedagogy',
        172	=> 'Sociology',
        173	=> 'History',
        174	=> 'Culturology',
        175	=> 'Foreign languages',
        176	=> 'Jurisprudence, law',
        177	=> 'Linguistics',
        178	=> 'Politics, political science',
        179	=> 'Mathematics',
        180	=> 'Technical literature',
        181	=> 'Physics',
        182	=> 'Philosophy',
        183	=> 'Chemistry',
        184	=> 'Geography',
        185	=> 'Other educational literature',
        186	=> 'Travel guides',
        187	=> 'Dictionaries',
        188	=> 'Manuals',
        189	=> 'Catalogs',
        190	=> 'Encyclopedias',
        191	=> 'References: other',
        192	=> 'Programming',
        193	=> 'Programs',
        194	=> 'Database',
        195	=> 'OS and networks',
        196	=> 'Internet',
        197	=> 'Computer hardware',
        198	=> 'Computers: other',
        199	=> 'Tales',
        200	=> 'Short stories',
        201	=> 'Essays',
        202	=> 'Essay',
        203	=> 'Newspapers',
        204	=> 'Magazines',
        205	=> 'Prose',
        207	=> 'Unassigned',
        208	=> 'Novels',
        209	=> 'Epic',
        210	=> 'Independent publishing',
    ];

    /**
     * @param ContainerInterface|null $container
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $em = $this->container->get('doctrine.orm.entity_manager');
        $genreRepo = $em->getRepository('AppBundle:Genre');
        $genres = $genreRepo->findAll();
        foreach ($genres as $genre) {
            $id = $genre->getId();
            $genre->setTitleEn($this->titles[$id]);
            $em->persist($genre);
        }
        $em->flush();
        $em->clear();
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {

    }
}
