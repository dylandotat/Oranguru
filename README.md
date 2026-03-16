# Oranguru

Oranguru is a fork of Dave Wilding’s Frogtab. The name Oranguru is based on the Pokémon because according to generation eight:

> [...] Oranguru skillfully gives instructions to other Pokémon.

Source: [Serebii](https://www.serebii.net/pokedex-swsh/oranguru/)

## Differences from Frogtab

Whilst Frogtab is a really good base to build on top of (I do really like it), I find that it is missing a good bit of features. One of the main ones for me is the syncing between devices. This is because I want to have my plain text todo on my iPhone and on my Laptop too.

Some other differences are:

- Supports docker
- Now a progressive web app
- Ability to delete Achievements
- Syncing of Achievements

## What is Frogtab anyway?

Frogtab is a piece of OSS software for the web that allows you to manage your todos. Unlike other todo applications Frogtab is really simple. At the end of each day, your todos from the ‘Today’ tab go into your ‘Inbox’. Once a todo is in your inbox you can either:

1. Do it as soon as possible
2. Do it later and hide the notification (by putting a hash ‘#’ in front of the todo)
3. Delete the todo.

Frogtab also has an Achievements system where, if you press CMD & K (CTRL & K on Windows), you can send the todo to achievements. On both Frogtab and Oranguru this is available, however, Oranguru also syncs these between devices too.

## Notes about committing

I currently do not accept commits, no matter how big or small they might be. I might look at your issues, but please do not submit code.

## Notes about data and software bugs

Oranguru can be buggy sometimes, expect it to have some bugs. This is largely because the changes after it forked away from Frogtab, are made with the assistance of AI. This is mostly because I did not want to learn PHP just for this.