# Refactoring Kata Test Upgrade Notes

Author of the changes: Florian Ajir ([GitHub](https://github.com/florianajir), [Email](mailto:florianajir@gmail.com))

## Rules

Rules to follow:

- [x] You must commit regularly
- [x] You must not modify code when comments explicitly forbid it

## Deliverables

Expected from me:

- [x] the link of the git repository: [GitHub repository](https://github.com/florianajir/backend-test-main)
- [x] several commits, with an explicit message each
  time: [GitHub repository](https://github.com/florianajir/backend-test-main/commits)
- [x] a file / message / email explaining your process and principles you've followed:

> ### Process
> 1. Download and extract codebase from zip sent by `clement.russo@convelio.com`
> 2. Open project in IDE (used phpstorm) and analyze source code
> 3. Init git repository and add files to tracking
> 4. Run make example in terminal to install dependencies and run the example
> 5. Add new make test command to run phpunit in php docker container
> 6. Refactoring `Quote.php` render functions signature (remove static)
> 7. Create `TemplateProcessorInterface.php` and implement `TemplateProcessorInterface` in `QuoteTemplateProcessor` and
     `UserTemplateProcessor`. Extract logic code from `TemplateManager` to the process functions and refactor logic
     (remove useless code, switch to `strtr` function for replacement using associative array, implement isProcessable
     with preg_match looking for regex pattern)
> 8. Make use of Processors in `TemplateManager.php` (using kind of Dependency Injection with no-BC tweak)
> 9. Implement `SiteTemplateProcessor` (and processors tests) to demonstrate extensibility mechanism
> 10. Push the commits to the `refactoring` branch and create a pull request to the main branch
> 11. Merge the PR and give access to `convelio-reviewer` GitHub user
> 12. Send via email to `clement.russo@convelio.com` the link to this documentation
>
> ### Principles
> * Robustness principle (No BC, full retro-compatibility)
> * **SOLID Principles** especially:
>   * Single Responsibility Principle (every class should have only one reason to change)
>   * Open–Closed Principle (no longer have to modify existing code when we create new entity or template)
>
> ### Changes
> * [UPDATE] `Makefile` (missing mbstring extension requirements)
> * [UPDATE] `composer.json` (missing mbstring extension requirements)
> * [UPDATE] `src/TemplateManager.php` (refactoring code using template processors)
> * [UPDATE] `src/Entity/Quote.php` (fetch/associate related objects on instantiation)
> * [ADD] `src/Processor/TemplateProcessorInterface.php` (define processor specs)
> * [ADD] `src/Processor/QuoteTemplateProcessor.php` (move the logic from manager and refactoring)
> * [ADD] `src/Processor/UserTemplateProcessor.php` (move the logic from manager and refactoring)
> * [ADD] `src/Processor/SiteTemplateProcessor.php` (extensibility implementation example)
> * [ADD] `tests/QuoteTemplateProcessorTest.php` (test the processor against regressions + placeholder substitutions)
> * [ADD] `tests/UserTemplateProcessorTest.php` (test the processor against regressions + placeholder substitutions)
> * [ADD] `tests/SiteTemplateProcessorTest.php` (test the new extensible processor)
>
> ### Thoughts
> #### PSR-4 namespace refactoring
> I briefly tried to define namespaces in class files to comply with
> [PSR-4 specification](https://www.php-fig.org/psr/psr-4/) but had not enough time to fix everything broken and turned
> my mind to keep structure as it was.
> #### Factory design pattern
> I began implementing the factory design pattern for Quote instantiation (a QuoteFactory class) to give responsibility
> for fetching related objects and inject them in quote constructor instead of identifiers, but it is mentioned in the
> QuoteRepository to not edit the method getById which is instantiating a Quote using his constructor. So I reverted it.
> #### Bridge design pattern
> I doubted about the right place of logic for quote formatting as text or html. Actually this is implemented in
> entity as accessors, but we can imagine a FormatterInterface::format(string) spec implemented in different classes
> like HtmlFormatter & TextFormatter as well as others if needed.
> I reverted it as there is no need for such a complexity given the explained needs.
> #### Commit `composer.lock`
> As explained in
> the [composer official documentation](https://getcomposer.org/doc/01-basic-usage.md#commit-your-composer-lock-file-to-version-control)
> It is recommended to commit `composer.lock` file on a shared project to share the same dependencies version.
> I kept it ignored (initially in `.gitignore`) because it is IMO out of scope and the impact on execution is relatively
> small due to the restricted amount of dependencies and the low risk to have broken things on different versions matrix
> (php restricted to v7.2).
> #### PHP upgrade
> I hesitated to upgrade php version to latest 8.1 in `composer.json` and `Makefile` docker instruction, but I think
> this is an expected limitation (even if not mentioned in `README.md`). If I had upgraded php to v8.1 I would have
> edited the code to take profit of the possibilities of hard typing class properties declaration or made use of null
> safe operator introduced in php 8 among other things.
> #### PSR-12 code style reformatting
> Every files committed were reformatted according to the [PSR-12 specification](https://www.php-fig.org/psr/psr-12/)