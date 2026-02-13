<?php

namespace App\Command;

use App\Repository\PostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:publish-scheduled-posts',
    description: 'Automatically publish posts that have reached their scheduled publication date',
)]
class PublishScheduledPostsCommand extends Command
{
    public function __construct(
        private PostRepository $postRepository,
        private EntityManagerInterface $entityManager
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        // Find all posts that should be published
        $posts = $this->postRepository->findScheduledForPublication();

        if (empty($posts)) {
            $io->info('No posts to publish at this time.');
            return Command::SUCCESS;
        }

        $publishedCount = 0;
        foreach ($posts as $post) {
            $post->setIsPublished(true);
            $publishedCount++;
            $io->writeln(sprintf(
                'Publishing post: "%s" (scheduled for %s)',
                $post->getTitle(),
                $post->getPublishedAt()->format('Y-m-d H:i:s')
            ));
        }

        $this->entityManager->flush();

        $io->success(sprintf('Successfully published %d post(s).', $publishedCount));

        return Command::SUCCESS;
    }
}
